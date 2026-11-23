<?php

namespace App\Http\Controllers;

use App\Support\Notify;
use Illuminate\Http\Request;

/**
 * Admin aur member dono ka notification controller.
 * Admin routes `auth:admin` guard me aur member routes `auth:web` guard me hain,
 * isliye $request->user() hamesha usi panel ka logged-in user deta hai.
 * Route names: admin.notifications.* aur member.notifications.* (routes/notifications.php dekho).
 */
class NotificationController extends Controller
{
    /** Member (Zentic) panel: notification icon => [icon class, notif-icon bg class] */
    public const ICONS = [
        'inquiry' => ['icon-message-square', 'bg-primary'],
        'contact' => ['icon-mail', 'bg-info'],
        'user'    => ['icon-user-plus', 'bg-success'],
        'product' => ['icon-package', 'bg-warning'],
        'profile' => ['icon-circle-user', 'bg-primary'],
        'survey'  => ['icon-clipboard-list', 'bg-info'],
        'bell'    => ['icon-bell', 'bg-primary'],
    ];

    /** "Enquiries" tab me ye types, baaki sab "Updates" tab me. */
    public const REQUEST_TYPES = ['product_inquiry', 'contact_inquiry', 'member_registered'];

    /**
     * Notification par click: read mark karo aur sahi page par bhejo.
     * Link click ke time type se banta hai (Notify::urlFor), save hua purana url sirf backup hai.
     */
    public function redirect(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        $url = Notify::urlFor($notification->data['type'] ?? null, $this->panel($request))
            ?? ($notification->data['url'] ?? null)
            ?? $this->home($request);

        return redirect($url);
    }

    /** "Mark all as read" button. */
    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /** Ek notification delete. */
    public function destroy(Request $request, string $id)
    {
        $request->user()->notifications()->where('id', $id)->delete();

        return back()->with('success', 'Notification deleted.');
    }

    /** "Clear read" button: padhe hue sab notifications delete. */
    public function clearRead(Request $request)
    {
        $request->user()->readNotifications()->delete();

        return back()->with('success', 'Read notifications cleared.');
    }

    /** Member panel: sab notifications ki list (route member.notification). ?tab=requests / system / unread */
    public function memberIndex(Request $request)
    {
        $user = $request->user();
        $tab  = in_array($request->tab, ['requests', 'system', 'unread'], true) ? $request->tab : 'all';

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

        $notifications = $query->paginate(15)->withQueryString();

        $stats = [
            'unread' => $user->unreadNotifications()->count(),
            'today'  => $user->notifications()->where('created_at', '>=', now()->startOfDay())->count(),
            'week'   => $user->notifications()->where('created_at', '>=', now()->startOfWeek())->count(),
            'total'  => $user->notifications()->count(),
        ];

        return view('member.notifications.index', compact('notifications', 'stats', 'tab'));
    }

    /** Admin panel: sab notifications ki list. */
    public function adminIndex(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /** Bell ke liye live data (har 30 sec JS se call hota hai, page reload ki zarurat nahi). */
    public function latest(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'items' => $user->unreadNotifications()->take(8)->get()->map(fn ($n) => [
                'title'   => $n->data['title'] ?? 'Notification',
                'message' => $n->data['message'] ?? '',
                'icon'    => $n->data['icon'] ?? 'bell',
                'type'    => $n->data['type'] ?? 'general',
                'time'    => $n->created_at->diffForHumans(),
                'url'     => route($this->panel($request) . '.notifications.open', $n->id),
            ]),
        ]);
    }

    /** Kaunse panel se request aayi: 'admin' ya 'member' (route name se). */
    private function panel(Request $request): string
    {
        return $request->routeIs('admin.*') ? 'admin' : 'member';
    }

    private function home(Request $request): string
    {
        return route($this->panel($request) . '.dashboard');
    }
}