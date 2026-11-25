<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SubCategory;
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
        // Har category ki kitni sub categories (app decide kare: sub category screen ya seedha products)
        $subCounts = SubCategory::selectRaw('category_id, COUNT(*) as total')->groupBy('category_id')->pluck('total', 'category_id');

        $categories = Market::tableCategories()
            ->when($request->boolean('only_with_products'), fn ($list) => $list->filter(fn ($c) => (int) ($base[$c->getKey()] ?? 0) > 0))
            ->map(fn ($category) => ApiData::category($category, [
                'all'  => (int) ($all[$category->getKey()] ?? 0),
                'have' => (int) ($have[$category->getKey()] ?? 0),
                'want' => (int) ($want[$category->getKey()] ?? 0),
            ]) + [
                'sub_categories_count' => (int) ($subCounts[$category->getKey()] ?? 0),
            ])
            ->values();

        return $this->ok($categories);
    }

    /**
     * GET /categories/{slug}?type=all|have|want&sort=latest|oldest|name&q=&sub_category=&page=
     *
     * sub_category (website jaisa flow ke liye):
     *   (nahi bheja)  → category ke SAARE products (purana behaviour, purane app ke liye)
     *   none          → sirf jin products ki sub category NULL hai (website ka category page)
     *   {id | slug}   → sirf us sub category ke products (website ka sub category page)
     *
     * Har response me "sub_categories" (is category ki sub categories + product count) aur
     * "sub_category" (selected wali, ya null) aata hai.
     */
    public function show(Request $request, string $slug)
    {
        $category = Market::findCategory($slug);
        if (! $category) {
            return $this->fail('Category not found.', 404);
        }

        $id   = $category->getKey();
        $type = in_array($request->query('type'), ['have', 'want'], true) ? $request->query('type') : null;
        $q    = trim((string) $request->query('q', ''));

        $subCategories = SubCategory::where('category_id', $id)->orderBy('name')->get();

        // sub_category param: null = sab, 'none' = bina sub category wale, warna id / slug
        $subParam    = $request->filled('sub_category') ? trim((string) $request->query('sub_category')) : null;
        $subCategory = null;
        if ($subParam !== null && $subParam !== 'none') {
            $subCategory = $subCategories->first(
                fn ($sub) => (string) $sub->id === $subParam || Market::subSlugOf($sub) === $subParam
            );
            if (! $subCategory) {
                return $this->fail('Sub category not found.', 404);
            }
        }

        // Har sub category me kitne products (type tab ke hisaab se)
        $subCounts = Market::productQuery()
            ->where(Market::categoryKey(), $id)
            ->whereNotNull('sub_category_id')
            ->when($type, fn ($query) => $query->where('listing_type', $type))
            ->selectRaw('sub_category_id, COUNT(*) as total')
            ->groupBy('sub_category_id')
            ->pluck('total', 'sub_category_id');

        // Bina sub category wale products kitne (category page ki "Other listings")
        $withoutSubCount = Market::productQuery()
            ->where(Market::categoryKey(), $id)
            ->whereNull('sub_category_id')
            ->when($type, fn ($query) => $query->where('listing_type', $type))
            ->count();

        $query = Market::productQuery()
            ->with(ApiData::PRODUCT_WITH)
            ->where(Market::categoryKey(), $id)
            ->when($subCategory, fn ($query) => $query->where('sub_category_id', $subCategory->id))
            ->when($subParam === 'none', fn ($query) => $query->whereNull('sub_category_id'))
            ->when($type, fn ($query) => $query->where('listing_type', $type))
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('product_tags', 'like', $like));
            });

        ProductController::applySort($query, $request->query('sort'));

        $products = $query->paginate($this->perPage($request))->withQueryString();

        return $this->paginated($products, fn ($product) => ApiData::productCard($product), [], [
            'category' => ApiData::category($category, [
                'all'  => (int) (Market::countsByCategoryId()[$id] ?? 0),
                'have' => (int) (Market::countsByCategoryId('have')[$id] ?? 0),
                'want' => (int) (Market::countsByCategoryId('want')[$id] ?? 0),
            ]),
            'sub_categories' => $subCategories->map(fn ($sub) => [
                'id'             => $sub->id,
                'name'           => $sub->name,
                'slug'           => Market::subSlugOf($sub),
                'products_count' => (int) ($subCounts[$sub->id] ?? 0),
            ])->values(),
            'without_sub_category_count' => $withoutSubCount,
            'sub_category' => $subCategory ? [
                'id'   => $subCategory->id,
                'name' => $subCategory->name,
                'slug' => Market::subSlugOf($subCategory),
            ] : null,
            'filter' => $subCategory ? 'sub_category' : ($subParam === 'none' ? 'without_sub_category' : 'all'),
        ]);
    }
}
