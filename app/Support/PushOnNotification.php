<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Notifications\Events\NotificationSent;

/**
 * Website jab bhi member ko bell notification deti hai (App\Support\Notify -> AppNotification, database),
 * usi waqt member ke phone par push bhi chala jata hai. Notify / AppNotification me koi change nahi.
 *
 * Register (sirf ek baar), app/Providers/AppServiceProvider.php ke boot() me:
 *     \Illuminate\Support\Facades\Event::listen(
 *         \Illuminate\Notifications\Events\NotificationSent::class,
 *         [\App\Support\PushOnNotification::class, 'handle']
 *     );
 * (Ye class jaan-boojh kar app/Listeners me nahi hai, taaki Laravel ise do baar register na kare.)
 */
class PushOnNotification
{
    public function handle(NotificationSent $event): void
    {
        if ($event->channel !== 'database' || ! $event->notification instanceof AppNotification) {
            return;
        }

        $user = $event->notifiable;
        if (! $user instanceof User || ! in_array($user->role, ['Farmer', 'Non Farmer'], true)) {
            return; // Admin ka push nahi (admin website panel use karta hai)
        }

        $notification = $event->notification;
        $id           = $event->response->id ?? $notification->id ?? '';
        $userId       = (int) $user->id;

        // Response jaane ke baad bhejo, taaki enquiry / approve wala page slow na ho
        dispatch(function () use ($userId, $notification, $id) {
            Push::toUser($userId, $notification->title, $notification->message, [
                'notification_id' => (string) $id,
                'type'            => $notification->type,
                'screen'          => ApiData::NOTIFICATION_SCREENS[$notification->type] ?? 'notifications',
            ]);
        })->afterResponse();
    }
}
