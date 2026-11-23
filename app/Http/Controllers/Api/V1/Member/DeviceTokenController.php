<?php

namespace App\Http\Controllers\Api\V1\Member;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\DeviceToken;
use Illuminate\Http\Request;

/**
 * Firebase (FCM) token save / delete. App login ke baad aur token refresh par bheje.
 */
class DeviceTokenController extends ApiController
{
    /** POST /me/device-token { token, platform: android|ios } */
    public function store(Request $request)
    {
        $data = $this->check($request, [
            'token'    => 'required|string|max:255',
            'platform' => 'nullable|in:android,ios',
        ]);

        // Ek phone par account badla to token naye user ka ho jaye
        DeviceToken::updateOrCreate(
            ['token' => $data['token']],
            ['user_id' => $request->user()->id, 'platform' => $data['platform'] ?? 'android', 'last_used_at' => now()]
        );

        return $this->ok(null, 'Device registered.');
    }

    /** DELETE /me/device-token { token } */
    public function destroy(Request $request)
    {
        $data = $this->check($request, ['token' => 'required|string|max:255']);

        DeviceToken::where('user_id', $request->user()->id)->where('token', $data['token'])->delete();

        return $this->ok(null, 'Device removed.');
    }
}
