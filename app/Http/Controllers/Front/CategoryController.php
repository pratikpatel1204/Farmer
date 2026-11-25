<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Support\Market;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $haveCounts = Market::countsByCategoryId('have');
        $wantCounts = Market::countsByCategoryId('want');

        $categories = Market::tableCategories()->each(function ($category) use ($haveCounts, $wantCounts) {
            $category->setAttribute('have_count', (int) ($haveCounts[$category->id] ?? 0));
            $category->setAttribute('want_count', (int) ($wantCounts[$category->id] ?? 0));
        });

        return view('front.category.index', compact('categories'));
    }

    /**
     * /category/{slug}            → us category ki sub categories (cards) + jin products ki sub category NULL hai
     * /category/{slug}/{subSlug}  → sirf us sub category ke products
     */
    public function show(Request $request, string $slug, ?string $subSlug = null)
    {
        $category = Market::findCategory($slug);
        abort_unless($category, 404);

        $type = in_array($request->query('type'), ['want', 'have'], true) ? $request->query('type') : null;
        $q = trim((string) $request->query('q'));

        // Is category ki sub categories (admin ke Sub Category Master se)
        $subCategories = SubCategory::where('category_id', $category->id)->orderBy('name')->get();

        $subCategory = null;
        if ($subSlug !== null) {
            $subCategory = $subCategories->first(
                fn($sub) => Market::subSlugOf($sub) === $subSlug || (string) $sub->id === $subSlug
            );
            abort_unless($subCategory, 404);
        }

        // Har sub category me kitne products (People Have / Want tab ke hisaab se)
        $subCounts = Market::productQuery()
            ->where(Market::categoryKey(), $category->id)
            ->whereNotNull('sub_category_id')
            ->when($type, fn($query) => $query->where('listing_type', $type))
            ->selectRaw('sub_category_id, COUNT(*) as total')
            ->groupBy('sub_category_id')
            ->pluck('total', 'sub_category_id');
        $subCategories->each(fn($sub) => $sub->setAttribute('products_count', (int) ($subCounts[$sub->id] ?? 0)));

        $query = Market::productQuery()
            ->with(['member.states', 'member.citys', 'categorys'])->whereNull('products.completed_at')
            ->where(Market::categoryKey(), $category->id)
            // Sub category page: sirf uske products. Category page: jinki sub category nahi hai (NULL)
            ->when(
                $subCategory,
                fn($query) => $query->where('sub_category_id', $subCategory->id),
                fn($query) => $query->whereNull('sub_category_id')
            )
            ->when($type, fn($query) => $query->where('listing_type', $type))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('title', 'like', "%{$q}%")->orWhere('product_tags', 'like', "%{$q}%");
                });
            });

        match ($request->query('sort')) {
            'oldest' => $query->orderBy('id'),
            'name'   => $query->orderBy('title'),
            default  => $query->orderByDesc('id'),
        };

        $products = $query->paginate(config('kisanmandi.per_page.products', 12))->withQueryString();

        // Sidebar: saari categories + count
        $counts = Market::countsByCategoryId();
        $categories = Market::tableCategories()
            ->each(fn($item) => $item->setAttribute('products_count', (int) ($counts[$item->id] ?? 0)));

        return view('front.category.show', compact('category', 'categories', 'products', 'type', 'subCategories', 'subCategory'));
    }
}
