<?php

namespace App\Http\Controllers\Api\V1\Member;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Profile;
use App\Support\ApiData;
use App\Support\MemberStats;
use Illuminate\Http\Request;

/**
 * /me, /me/dashboard, /me/team
 */
class AccountController extends ApiController
{
    /** GET /me */
    public function me(Request $request)
    {
        return $this->ok(ApiData::me($request->user()));
    }

    /** GET /me/dashboard (website member dashboard ke 3 section + Refer & Earn) */
    public function dashboard(Request $request)
    {
        $user    = $request->user();
        $profile = Profile::where('user_id', $user->id)->first();

        return $this->ok([
            'welcome_name'   => $user->full_name,
            'profile_exists' => (bool) $profile,
            'profile_status' => $profile->status ?? null,
            'products'       => MemberStats::products($user->id),
            'inquiries'      => MemberStats::inquiries($user->id),
            'team'           => MemberStats::team($user->unique_id)['stats'],
            'referral_link'  => ApiData::referralLink($user),
            'public_profile_url' => ($profile->status ?? null) === 'Approved' ? ApiData::webUrl('profile/' . $user->unique_id) : null,
        ]);
    }

    /** GET /me/team?level=1&page=1 */
    public function team(Request $request)
    {
        $level = max(1, (int) $request->query('level', 1));
        $team  = MemberStats::team($request->user()->unique_id, $level);

        $perPage = $this->perPage($request, 20, 50);
        $page    = max(1, (int) $request->query('page', 1));
        $total   = count($team['members']);

        return $this->ok([
            'stats'         => $team['stats'],
            'levels'        => $team['levels'],
            'level'         => $level,
            'members'       => array_slice($team['members'], ($page - 1) * $perPage, $perPage),
            'referral_link' => ApiData::referralLink($request->user()),
        ], 'OK', 200, ['meta' => [
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
            'per_page'     => $perPage,
            'total'        => $total,
        ]]);
    }
}
