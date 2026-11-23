<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Support\ApiData;
use App\Support\Market;
use Illuminate\Http\Request;

/**
 * Public member profiles (website /profile/{unique_id} jaisa).
 * Sirf Approved profile + active Farmer / Non Farmer. Bank / KYC / mobile / email kabhi nahi.
 */
class ProfileController extends ApiController
{
    public static function approvedMembers()
    {
        return User::query()
            ->with(['profile', 'states', 'citys'])
            ->whereIn('role', ['Farmer', 'Non Farmer'])
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->whereHas('profile', fn ($query) => $query->where('status', 'Approved'));
    }

    /**
     * NAYA: Approved members, naam (full_name) se search. Poora unique_id bhi chalta hai.
     * Jo naam search word se shuru ho wo pehle. 2 letter se kam ho to kuch nahi.
     */
    public static function searchByName(string $q)
    {
        $q = trim($q);
        $safe = addcslashes($q, '%_\\');

        return self::approvedMembers()
            ->when(mb_strlen($q) < 2, fn ($query) => $query->whereRaw('1 = 0'))
            ->where(function ($w) use ($q, $safe) {
                $w->where('full_name', 'like', "%{$safe}%")
                    ->orWhere('unique_id', $q);
            })
            ->orderByRaw('CASE WHEN full_name LIKE ? THEN 0 ELSE 1 END', ["{$safe}%"])
            ->orderBy('full_name');
    }

    /** GET /profiles?role=all|farmer|non_farmer&q=NAME&page=&per_page= */
    public function index(Request $request)
    {
        $role = match ($request->query('role')) {
            'farmer'     => 'Farmer',
            'non_farmer' => 'Non Farmer',
            default      => null,
        };

        $q = trim((string) $request->query('q'));

        // NAYA: q ho to naam se search, warna sab (naye pehle)
        $members = ($q !== '' ? self::searchByName($q) : self::approvedMembers()->orderByDesc('id'))
            ->when($role, fn ($query) => $query->where('role', $role))
            ->paginate($this->perPage($request, 16, 40))
            ->withQueryString();

        return $this->paginated($members, fn ($user) => ApiData::memberCard($user));
    }

    /** GET /profiles/{uniqueId} */
    public function show(string $uniqueId)
    {
        $member = self::approvedMembers()->where('unique_id', $uniqueId)->first();

        if (! $member) {
            return $this->fail('Profile not found.', 404);
        }

        $profile = $member->profile;

        $products = Market::productQuery()
            ->with(ApiData::PRODUCT_WITH)
            ->where('member_id', $member->id)
            ->orderByDesc('id')
            ->get();

        return $this->ok([
            'member'     => ApiData::memberCard($member) + [
                'pincode' => $member->pincode,
                'since'   => optional($member->created_at)->format('M Y'),
            ],
            'about_html' => Market::safeHtml($profile->description),
            'farm'       => [
                'established'  => $profile->establishment_year,
                'workers'      => $profile->number_of_workers,
                'land_sq_yard' => $profile->area_in_square_yard,
            ],
            'links'      => (object) Market::profileLinks($profile),
            'photos'     => ApiData::urls($profile->company_images),
            'video'      => ApiData::url($profile->company_video),
            'stats'      => [
                'listings' => $products->count(),
                'have'     => $products->where('listing_type', 'have')->count(),
                'want'     => $products->where('listing_type', 'want')->count(),
            ],
            'products'   => $products->map(fn ($product) => ApiData::productCard($product))->values(),
            'share_url'  => ApiData::webUrl('profile/' . $member->unique_id),
        ]);
    }
}
