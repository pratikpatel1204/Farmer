<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ProductAttribute;
use App\Models\User;
use App\Support\ApiData;
use App\Support\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * I Want / I Have / Search listing + product detail.
 * Sirf Approved product, active owner, aur Completed nahi (Market::productQuery).
 */
class ProductController extends ApiController
{
    /**
     * GET /products?type=want|have&category=ID&q=&sort=latest|oldest|name&member=UNIQUE_ID&page=&per_page=
     * type khali = dono (search)
     * q = product name, tag, category ya member (profile) ka naam.
     * Search (type khali + q) me response ke saath "profiles" bhi aata hai: naam se milte profiles (page 1 par, max 12).
     */
    public function index(Request $request)
    {
        $type       = in_array($request->query('type'), ['have', 'want'], true) ? $request->query('type') : null;
        $categoryId = (int) $request->query('category') ?: null;
        $q          = trim((string) $request->query('q'));
        $memberId   = null;

        if ($request->filled('member')) {
            $memberId = User::where('unique_id', $request->query('member'))->value('id') ?? 0;
        }

        $query = Market::productQuery()
            ->with(ApiData::PRODUCT_WITH)
            ->when($type, fn ($query) => $query->where('listing_type', $type))
            ->when($categoryId, fn ($query) => $query->where(Market::categoryKey(), $categoryId))
            ->when($memberId !== null, fn ($query) => $query->where('member_id', $memberId))
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                        ->orWhere('product_tags', 'like', $like)
                        ->orWhereHas('categorys', fn ($c) => $c->where('name', 'like', $like))
                        // NAYA: member (profile) ke naam se bhi product mile
                        ->orWhereHas('member', fn ($m) => $m->where('full_name', 'like', $like));
                });
            });

        self::applySort($query, $request->query('sort'));

        $products = $query->paginate($this->perPage($request))->withQueryString();

        // "14 listings" (filter / search ke bina is type me kitne)
        $totalAll = Market::productQuery()->when($type, fn ($query) => $query->where('listing_type', $type))->count();

        // NAYA: search me naam se milte profiles (website search page jaisa, products ke neeche)
        $extra = [];
        if ($type === null && $memberId === null) {
            $extra['profiles'] = ($q !== '' && $products->currentPage() === 1)
                ? ProfileController::searchByName($q)->limit(12)->get()->map(fn ($user) => ApiData::memberCard($user))->values()
                : [];
        }

        return $this->paginated($products, fn ($product) => ApiData::productCard($product), ['total_all' => $totalAll], $extra);
    }

    /** GET /products/{slug}  (slug na ho to id se bhi khulta hai) */
    public function show(Request $request, string $slug)
    {
        $product = self::findPublic($slug, ['member.states', 'member.citys', 'member.profile', 'categorys']);

        if (! $product) {
            return $this->fail('This listing is no longer available.', 404);
        }

        $member  = $product->member;
        $profile = $member?->profile;
        $type    = Market::typeOf($product);

        $images = array_values(array_unique(array_filter(array_merge(
            [ApiData::url($product->main_image)],
            ApiData::urls($product->sub_images)
        ))));

        $tags = array_values(array_filter(array_map('trim', explode(',', (string) $product->product_tags))));

        $attributes = ProductAttribute::where('product_id', $product->id)
            ->orderBy('id')
            ->get(['label', 'value'])
            ->map(fn ($row) => ['label' => $row->label, 'value' => $row->value]);

        $categoryKey = Market::categoryKey();
        $related = Market::productQuery()
            ->with(ApiData::PRODUCT_WITH)
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product, $categoryKey) {
                $query->where($categoryKey, $product->{$categoryKey})
                    ->orWhere('member_id', $product->member_id);
            })
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(fn ($item) => ApiData::productCard($item));

        // Token bheja ho to: apni listing hai? 24 ghante me enquiry bhej chuka?
        $viewer = null;
        if ($user = auth('sanctum')->user()) {
            $viewer = [
                'is_own'           => (int) $product->member_id === (int) $user->id,
                'already_enquired' => DB::table('product_inquiries')
                    ->where('product_id', $product->id)
                    ->where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subDay())
                    ->exists(),
            ];
        }

        return $this->ok([
            'id'          => $product->id,
            'slug'        => $product->slug ?: (string) $product->id,
            'title'       => $product->title,
            'type'        => $type,
            'sku'         => $product->sku,
            'images'      => $images,
            'category'    => ApiData::category($product->categorys),
            'tags'        => $tags,
            'description' => ApiData::plainText($product->description),
            'attributes'  => $attributes,
            'brochure'    => ApiData::url($product->brochure),
            'posted_at'   => ApiData::date($product->created_at),
            'location'    => ApiData::place($member),
            'seller'      => $member ? [
                'unique_id'  => $member->unique_id,
                'name'       => $member->full_name,
                'role'       => $member->role,
                'image'      => ApiData::url($member->profile_image),
                'initials'   => ApiData::initials($member->full_name),
                'verified'   => ($profile->status ?? null) === 'Approved',
                'has_profile'=> ($profile->status ?? null) === 'Approved',
                'location'   => ApiData::place($member),
                'since'      => $profile->establishment_year ?? optional($member->created_at)->format('Y'),
                'about_html' => $profile ? Market::safeHtml($profile->description) : '',
                'links'      => $profile ? Market::profileLinks($profile) : (object) [],
            ] : null,
            'related'     => $related,
            'share_url'   => ApiData::webUrl('product/' . ($product->slug ?: $product->id)),
            'viewer'      => $viewer,
        ]);
    }

    /** Public product slug (ya id) se. Na mile to null. */
    public static function findPublic(string $slug, array $with = [])
    {
        $query = Market::productQuery()->with($with);

        $product = (clone $query)->where('slug', $slug)->first();
        if (! $product && ctype_digit($slug)) {
            $product = (clone $query)->where('id', (int) $slug)->first();
        }

        return $product;
    }

    public static function applySort($query, ?string $sort): void
    {
        match ($sort) {
            'oldest' => $query->orderBy('id'),
            'name'   => $query->orderBy('title'),
            default  => $query->orderByDesc('id'),
        };
    }
}
