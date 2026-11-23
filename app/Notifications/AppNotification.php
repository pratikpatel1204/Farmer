<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Ek hi notification class jo admin aur member dono ke liye kaam karti hai.
 * Laravel ki `notifications` table me row banti hai (database channel).
 *
 * data column me ye save hota hai:
 *   title   - bold heading (e.g. "New product enquiry")
 *   message - ek line detail
 *   url     - click karne par kahan jana hai
 *   icon    - icon ka naam (bell, inquiry, contact, user, product, profile, survey)
 *   type    - event ka naam (product_inquiry, contact_inquiry, ...)
 *
 * Seedha use mat karo, App\Support\Notify ke methods use karo.
 */
class AppNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public ?string $url = null,
        public string $icon = 'bell',
        public string $type = 'general',
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
            'icon'    => $this->icon,
            'type'    => $this->type,
        ];
    }
}