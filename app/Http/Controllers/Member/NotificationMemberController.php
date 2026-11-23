<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationMemberController extends Controller
{
    public function notification(Request $request)
    {
        $notificationsQuery = Notification::where('member_type', 'general');
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'Seller') {
                $notificationsQuery = Notification::where(function ($query) use ($user) {
                    $query->where('member_type', 'general')->orWhere('member_type', 'all');
                });
            }
        }

        $notifications = $notificationsQuery->latest()->get();

        return view('member.notification', compact('notifications'));
    }
}
