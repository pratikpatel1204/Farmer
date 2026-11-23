<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Market;

/**
 * Member ka public profile page: /profile/{unique_id}  (jaise /profile/NFR547895)
 * Route:
 *   Route::get('profile/{uniqueId}', [ProfileController::class, 'show'])->name('profile.show');
 *
 * Sirf Approved profile + active Farmer / Non Farmer dikhta hai, warna 404.
 * Bank, account number, IFSC, Aadhaar, PAN, KYC photo, email, mobile, password, OTP
 * kabhi view me nahi bheje jaate. Sirf SELECT query.
 */
class ProfileController extends Controller
{
    public function show(string $uniqueId)
    {
        $member = User::query()
            ->with(['profile', 'states', 'citys'])
            ->where('unique_id', $uniqueId)
            ->whereIn('role', ['Farmer', 'Non Farmer'])
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->whereHas('profile', fn ($query) => $query->where('status', 'Approved'))
            ->firstOrFail();

        $profile = $member->profile;

        // View ko sirf public cheezein (bank / KYC / contact wale columns yahan se aage nahi jaate)
        $details = [
            'name'        => $member->full_name,
            'role'        => $member->role,
            'unique_id'   => $member->unique_id,
            'image'       => $member->profile_image,
            'city'        => Market::placeName($member->citys, $member->city),
            'state'       => Market::placeName($member->states, $member->state),
            'area'        => $member->area,
            'pincode'     => $member->pincode,
            'since'       => optional($member->created_at)->format('M Y'),
            'description' => Market::safeHtml($profile->description),
            'established' => $profile->establishment_year,
            'workers'     => $profile->number_of_workers,
            'land'        => $profile->area_in_square_yard,
            'links'       => Market::profileLinks($profile),
            'photos'      => Market::imageList($profile->company_images),
            'video'       => $profile->company_video,
        ];

        $products = Market::productQuery()
            ->with(['member.states', 'member.citys', 'categorys'])->where('products.status', 'Approved')->whereNull('products.completed_at') 
            ->where('member_id', $member->id)
            ->orderByDesc('id')
            ->get();

        $haveProducts = $products->where('listing_type', 'have')->values();
        $wantProducts = $products->where('listing_type', 'want')->values();

        return view('front.profiles.show', compact('details', 'products', 'haveProducts', 'wantProducts'));
    }
}
