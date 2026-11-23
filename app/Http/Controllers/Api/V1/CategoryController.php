<?php

namespace App\Http\Controllers\Api\V1;

use App\Support\ApiData;
use App\Support\Market;
use Illuminate\Http\Request;

/**
 * Categories (categories table) + ek category ke products.
 */
class CategoryController extends ApiController
{
    /**
     * GET /categories?type=have|want&only_with_products=1
     * counts: all / have / want (type do to sirf us type wale categories filter hoti hain)
     */
    public function index(Request $request)
    {
        $type = in_array($request->query('type'), ['have', 'want'], true) ? $request->query('type') : null;

        $all  = Market::countsByCategoryId();
        $have = Market::countsByCategoryId('have');
        $want = Market::countsByCategoryId('want');
        $base = $type === 'have' ? $have : ($type === 'want' ? $want : $all);

        $categories = Market::tableCategories()
            ->when($request->boolean('only_with_products'), fn ($list) => $list->filter(fn ($c) => (int) ($base[$c->getKey()] ?? 0) > 0))
            ->map(fn ($category) => ApiData::category($category, [
                'all'  => (int) ($all[$category->getKey()] ?? 0),
                'have' => (int) ($have[$category->getKey()] ?? 0),
                'want' => (int) ($want[$category->getKey()] ?? 0),
            ]))
            ->values();

        return $this->ok($categories);
    }

    /** GET /categories/{slug}?type=all|have|want&sort=latest|oldest|name&page= */
    public function show(Request $request, string $slug)
    {
        $category = Market::findCategory($slug);
        if (! $category) {
            return $this->fail('Category not found.', 404);
        }

        $id   = $category->getKey();
        $type = in_array($request->query('type'), ['have', 'want'], true) ? $request->query('type') : null;

        $query = Market::productQuery()
            ->with(ApiData::PRODUCT_WITH)
            ->where(Market::categoryKey(), $id)
            ->when($type, fn ($q) => $q->where('listing_type', $type));

        ProductController::applySort($query, $request->query('sort'));

        $products = $query->paginate($this->perPage($request))->withQueryString();

        return $this->paginated($products, fn ($product) => ApiData::productCard($product), [], [
            'category' => ApiData::category($category, [
                'all'  => (int) (Market::countsByCategoryId()[$id] ?? 0),
                'have' => (int) (Market::countsByCategoryId('have')[$id] ?? 0),
                'want' => (int) (Market::countsByCategoryId('want')[$id] ?? 0),
            ]),
        ]);
    }
}
