<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Market;
use Illuminate\Http\Request;

/**
 * I Want / I Have listing + product detail (Eloquent: Product -> member, categorys).
 * Routes:
 *   Route::get('i-want', [ProductController::class, 'want'])->name('products.want');
 *   Route::get('i-have', [ProductController::class, 'have'])->name('products.have');
 *   Route::get('search', [ProductController::class, 'search'])->name('products.search');
 *   Route::get('product/{slug}', [ProductController::class, 'show'])->name('products.show');
 *
 * Search: product name, tag, category + member (profile) name. Neeche matching profiles bhi.
 *
 * Sirf SELECT query hai, database me kuch change nahi hota.
 */
class ProductController extends Controller
{
    public function want(Request $request)
    {
        return $this->listing($request, 'want');
    }

    public function have(Request $request)
    {
        return $this->listing($request, 'have');
    }

    /** Header ka search box: I Want + I Have dono me */
    public function search(Request $request)
    {
        return $this->listing($request, null);
    }

    private function listing(Request $request, ?string $type)
    {
        $categoryKey = Market::categoryKey();
        $categoryId = (int) $request->query('category') ?: null;
        $q = trim((string) $request->query('q'));

        $query = Market::productQuery()
            ->with(['member.states', 'member.citys', 'categorys'])
            ->when($type, fn ($query) => $query->where('listing_type', $type))
            ->when($categoryId, fn ($query) => $query->where($categoryKey, $categoryId))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('title', 'like', "%{$q}%")
                        ->orWhere('product_tags', 'like', "%{$q}%")
                        ->orWhereHas('categorys', fn ($c) => $c->where('name', 'like', "%{$q}%"))
                        // NAYA: member (profile) ke naam se bhi product mile
                        ->orWhereHas('member', fn ($m) => $m->where('full_name', 'like', "%{$q}%"));
                });
            });

        match ($request->query('sort')) {
            'oldest' => $query->orderBy('id'),
            'name'   => $query->orderBy('title'),
            default  => $query->orderByDesc('id'),
        };

        $products = $query->paginate(config('kisanmandi.per_page.products', 12))->withQueryString();

        // Sidebar: categories table se, sirf wahi jisme product hain (+ jo abhi chuni hai)
        $counts = Market::countsByCategoryId($type);

        $categories = Market::tableCategories()
            ->filter(fn ($category) => isset($counts[$category->id]) || $category->id === $categoryId)
            ->each(fn ($category) => $category->setAttribute('products_count', (int) ($counts[$category->id] ?? 0)))
            ->values();

        $activeCategory = $categoryId ? $categories->firstWhere('id', $categoryId) : null;
        $totalCount = (int) $counts->sum();

        // NAYA: header search (type = null) me naam se milte hue profiles bhi
        $profiles = $type === null ? $this->searchProfiles($q) : collect();

        return view('front.products.index', compact('products', 'type', 'categories', 'activeCategory', 'totalCount', 'profiles'));
    }

    /**
     * NAYA: Profile name se search (sirf Approved profile + active Farmer / Non Farmer).
     * Naam jo search word se shuru hota hai wo pehle aata hai.
     */
    private function searchProfiles(string $q, int $limit = 12)
    {
        if (mb_strlen($q) < 2) {
            return collect();
        }

        $safe = addcslashes($q, '%_\\');

        return User::with(['profile', 'states', 'citys'])
            ->whereIn('role', ['Farmer', 'Non Farmer'])
            ->where('users.status', 1)
            ->whereNull('users.deleted_at')
            ->whereHas('profile', fn ($p) => $p->where('status', 'Approved'))
            ->where(function ($w) use ($q, $safe) {
                $w->where('full_name', 'like', "%{$safe}%")
                    ->orWhere('unique_id', $q);
            })
            ->orderByRaw('CASE WHEN full_name LIKE ? THEN 0 ELSE 1 END', ["{$safe}%"])
            ->orderBy('full_name')
            ->limit($limit)
            ->get();
    }

    public function show(string $slug)
    {
        $product = Market::productQuery()
            ->with(['member.states', 'member.citys', 'member.profile', 'categorys'])
            ->where('slug', $slug)
            ->firstOrFail();

        $type = Market::typeOf($product);
        $member = $product->member;
        $profile = $member?->profile;

        $images = array_values(array_unique(array_filter(array_merge(
            [$product->main_image],
            Market::imageList($product->sub_images)
        ))));

        // People Want product me koi image nahi: category ki image dikhao
        if (!$images && $type === 'want' && ($fallback = Market::productImage($product))) {
            $images = [$fallback];
        }

        // Social links profiles ke alag columns se (facebook, instagram, youtube, linkedin)
        $socials = collect(Market::profileLinks($profile))->except('website')->all();

        // Specification tab: product_attributes (label / value)
        $attributes = \Illuminate\Support\Facades\Schema::hasTable('product_attributes')
            ? \Illuminate\Support\Facades\DB::table('product_attributes')
                ->where('product_id', $product->id)
                ->orderBy('id')
                ->get(['label', 'value'])
                ->filter(fn ($row) => trim((string) $row->label) !== '' && trim((string) $row->value) !== '')
                ->values()
            : collect();

        $categoryKey = Market::categoryKey();
        $related = Market::productQuery()
            ->with(['member.states', 'member.citys', 'categorys'])
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product, $categoryKey) {
                $query->where($categoryKey, $product->{$categoryKey})
                    ->orWhere('member_id', $product->member_id);
            })
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('front.products.show', compact('product', 'type', 'member', 'profile', 'images', 'socials', 'attributes', 'related'));
    }
}
