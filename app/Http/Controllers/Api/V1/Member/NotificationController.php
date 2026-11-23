<?php

namespace App\Http\Controllers\Api\V1\Member;

use App\Http\Controllers\Api\V1\ApiController;
use App\Support\ApiData;
use Illuminate\Http\Request;

/**
 * Member ki bell notifications (website wali hi `notifications` table, App\Support\Notify se bante hain).
 * Tabs website jaise: all / unread / requests (Enquiries) / system (Updates)
 */
class NotificationController extends ApiController
{
    /** Website ka NotificationController::REQUEST_TYPES */
    private const REQUEST_TYPES = ['product_inquiry', 'contact_inquiry', 'member_registered'];

    /** GET /me/notifications?tab=all|unread|requests|system&page= */
    public function index(Request $request)
    {
        $user  = $request->user();
        $tab   = in_array($request->query('tab'), ['requests', 'system', 'unread'], true) ? $request->query('tab') : 'all';
        $query = $user->notifications();

        if ($tab === 'unread') {
            $query->whereNull('read_at');
        } elseif ($tab !== 'all') {
            $match = function ($q) {
                foreach (self::REQUEST_TYPES as $type) {
                    $q->orWhere('data', 'like', '%"type":"' . $type . '"%');
                }
            };
            $tab === 'requests' ? $query->where($match) : $query->whereNot($match);
        }

        $notifications = $query->paginate($this->perPage($request, 15, 50))->withQueryString();

        return $this->paginated($notifications, fn ($n) => ApiData::notification($n), [
            'tab'   => $tab,
            'stats' => [
                'unread' => $user->unreadNotifications()->count(),
                'today'  => $user->notifications()->where('created_at', '>=', now()->startOfDay())->count(),
                'week'   => $user->notifications()->where('created_at', '>=', now()->startOfWeek())->count(),
                'total'  => $user->notifications()->count(),
            ],
        ]);
    }

    /** GET /me/notifications/unread-count */
    public function unreadCount(Request $request)
    {
        return $this->ok(['count' => $request->user()->unreadNotifications()->count()]);
    }

    /** POST /me/notifications/{id}/read */
    public function read(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if (! $notification) {
            return $this->fail('Notification not found.', 404);
        }

        $notification->markAsRead();

        return $this->ok(ApiData::notification($notification));
    }

    /** POST /me/notifications/read-all */
    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->ok(null, 'All notifications marked as read.');
    }

    /** DELETE /me/notifications/{id} */
    public function destroy(Request $request, string $id)
    {
        $deleted = $request->user()->notifications()->where('id', $id)->delete();
        return $deleted ? $this->ok(null, 'Notification deleted.') : $this->fail('Notification not found.', 404);
    }

    /** DELETE /me/notifications/read  (padhe hue sab delete) */
    public function clearRead(Request $request)
    {
        $request->user()->readNotifications()->delete();
        return $this->ok(null, 'Read notifications cleared.');
    }
}
