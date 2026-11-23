<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Token valid hai lekin account band (status 0) / delete / Admin ho to app use nahi kar sakta.
 * Website ke login jaisa hi rule. Band account ka token yahin delete ho jata hai.
 */
class ApiActiveMember
{
    public const ROLES = ['Farmer', 'Non Farmer'];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $blocked = ! $user
            || (int) $user->status !== 1
            || ! empty($user->deleted_at)
            || ! in_array($user->role, self::ROLES, true);

        if ($blocked) {
            $user?->currentAccessToken()?->delete();

            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact support to activate.',
            ], 403);
        }

        return $next($request);
    }
}
