<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
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

    public function show(Request $request, string $slug)
    {
        $category = Market::findCategory($slug);
        abort_unless($category, 404);

        $type = in_array($request->query('type'), ['want', 'have'], true) ? $request->query('type') : null;
        $q = trim((string) $request->query('q'));

        $query = Market::productQuery()
            ->with(['member.states', 'member.citys', 'categorys'])->whereNull('products.completed_at')  
            ->where(Market::categoryKey(), $category->id)
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

        return view('front.category.show', compact('category', 'categories', 'products', 'type'));
    }
}
