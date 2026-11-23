<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Saare notifications yahin se bhejo. Har method try/catch me hai,
 * isliye notification fail ho jaye to bhi enquiry/register/approve ka kaam nahi rukta.
 *
 * Kaun kya paata hai:
 *   productInquiry()   -> product ka maalik (member) + sab Admin
 *   contactInquiry()   -> sab Admin
 *   memberRegistered() -> sab Admin (+ naye member ko welcome)
 *   productAdded()     -> sab Admin (product approval ke liye)
 *   productStatus()    -> product ka maalik (member)
 *   profileSubmitted() -> sab Admin
 *   profileStatus()    -> profile ka maalik (member)
 *   surveySubmitted()  -> sab Admin
 *
 * Link ke liye route names neeche ROUTES me hain. Jo route aapke project me nahi hai
 * use skip karke agla try hota hai, last me dashboard.
 */
class Notify
{
    /** Notification par click karne se kaunsa page khulega (aapke web.php ke route names). Route na mile to dashboard. */
    private const ROUTES = [
        'admin.product_inquiries' => ['admin.product.inquiries.list', 'admin.dashboard'],
        'admin.contact'           => ['admin.contact.inquiries.list', 'admin.dashboard'],
        'admin.members'           => ['admin.member.details', 'admin.dashboard'],
        'admin.products'          => ['admin.product.list', 'admin.dashboard'],
        'admin.profiles'          => ['admin.member.details', 'admin.dashboard'],
        'admin.survey'            => ['admin.survey.index', 'admin.dashboard'],
        'member.inquiries'        => ['member.products.inquiries.list', 'member.dashboard'],
        'member.products'         => ['member.products', 'member.dashboard'],
        'member.profile'          => ['member.profile', 'member.dashboard'],
    ];

    /**
     * Notification type => kaunsa page (ROUTES ki key). Click ke time isi se link banta hai,
     * isliye purane notifications (jinme galat/dashboard link save ho gaya tha) bhi sahi page kholte hain.
     */
    private const TYPE_ROUTES = [
        'admin' => [
            'product_inquiry'   => 'admin.product_inquiries',
            'contact_inquiry'   => 'admin.contact',
            'member_registered' => 'admin.members',
            'product_added'     => 'admin.products',
            'profile_submitted' => 'admin.profiles',
            'survey_submitted'  => 'admin.survey',
        ],
        'member' => [
            'product_inquiry' => 'member.inquiries',
            'product_status'  => 'member.products',
            'profile_status'  => 'member.profile',
            'welcome'         => 'member.profile',
        ],
    ];

    /** Click par kahan bhejna hai: notification ke type + panel (admin/member) se. Na mile to null. */
    public static function urlFor(?string $type, string $panel): ?string
    {
        $key = self::TYPE_ROUTES[$panel][$type ?? ''] ?? null;

        return $key ? self::link($key) : null;
    }

    /* ------------------------------------------------------------------ */
    /*  Events                                                            */
    /* ------------------------------------------------------------------ */

    /** Kisi ne product par enquiry bheji. $product me id, member_id, name/title hona chahiye. */
    public static function productInquiry(object $product, string $senderName): void
    {
        $name = self::productName($product);

        self::toUser($product->member_id ?? null, new AppNotification(
            'New enquiry on your product',
            "{$senderName} sent an enquiry for \"{$name}\".",
            self::link('member.inquiries'),
            'inquiry',
            'product_inquiry'
        ));

        self::toAdmins(new AppNotification(
            'New product enquiry',
            "{$senderName} enquired about \"{$name}\".",
            self::link('admin.product_inquiries'),
            'inquiry',
            'product_inquiry'
        ));
    }

    /** Contact Us form se message aaya. */
    public static function contactInquiry(string $name, ?string $subject = null): void
    {
        self::toAdmins(new AppNotification(
            'New contact message',
            $name . ($subject ? " - {$subject}" : ' sent a message.'),
            self::link('admin.contact'),
            'contact',
            'contact_inquiry'
        ));
    }

    /** Naya member register hua (Farmer / Non Farmer). */
    public static function memberRegistered(User $user): void
    {
        $who = $user->full_name ?? $user->name ?? 'A new member';

        self::toAdmins(new AppNotification(
            'New member registered',
            "{$who} ({$user->role}) joined" . ($user->unique_id ? " - ID {$user->unique_id}." : '.'),
            self::link('admin.members'),
            'user',
            'member_registered'
        ));

        self::toUser($user->id, new AppNotification(
            'Welcome to ' . config('app.name'),
            'Complete your business profile so buyers can find you.',
            self::link('member.profile'),
            'profile',
            'welcome'
        ));
    }

    /** Member ne naya product add kiya - admin approve karega. */
    public static function productAdded(object $product): void
    {
        $owner = User::find($product->member_id ?? 0);

        self::toAdmins(new AppNotification(
            'New product for approval',
            '"' . self::productName($product) . '" added by ' . ($owner->full_name ?? $owner->name ?? 'a member') . '.',
            self::link('admin.products'),
            'product',
            'product_added'
        ));
    }

    /** Admin ne product Approve / Reject kiya. $status = 'Approved' / 'Rejected' / 1 / 2 jo bhi DB me hai. */
    public static function productStatus(object $product, $status, ?string $reason = null): void
    {
        $label = self::statusLabel($status);
        $name  = self::productName($product);

        $message = $label === 'Rejected'
            ? "Your product \"{$name}\" was rejected." . ($reason ? " Reason: {$reason}" : ' Please update and submit again.')
            : "Your product \"{$name}\" is now {$label}" . ($label === 'Approved' ? ' and live on the website.' : '.');

        self::toUser($product->member_id ?? null, new AppNotification(
            "Product {$label}",
            $message,
            self::link('member.products'),
            'product',
            'product_status'
        ));
    }

    /**
     * Member ne profile bhara / update kiya - admin approve karega.
     * About, KYC, images, video sab alag save hote hain, isliye ek member ka
     * ye notification 30 minute me sirf ek baar jaata hai (admin ko spam nahi).
     */
    public static function profileSubmitted(User $user): void
    {
        if (! Cache::add('notify_profile_submitted_' . $user->id, 1, now()->addMinutes(30))) {
            return;
        }

        self::toAdmins(new AppNotification(
            'Profile waiting for approval',
            ($user->full_name ?? $user->name ?? 'A member') . ' submitted the business profile.',
            self::link('admin.profiles'),
            'profile',
            'profile_submitted'
        ));
    }

    /** Admin ne profile Approve / Pending / Reject kiya. Reject par reason bhi bhej sakte ho. */
    public static function profileStatus(int $userId, $status, ?string $reason = null): void
    {
        $label = self::statusLabel($status);

        $message = match ($label) {
            'Approved' => 'Your business profile is approved and visible on the website.',
            'Rejected' => 'Your business profile was rejected.' . ($reason ? " Reason: {$reason}" : ' Please update it and submit again.'),
            default    => "Your business profile status is now {$label}.",
        };

        self::toUser($userId, new AppNotification(
            "Profile {$label}",
            $message,
            self::link('member.profile'),
            'profile',
            'profile_status'
        ));
    }

    /** Member ne survey submit kiya. */
    public static function surveySubmitted(User $user, ?string $surveyTitle = null): void
    {
        self::toAdmins(new AppNotification(
            'New survey response',
            ($user->full_name ?? $user->name ?? 'A member') . ' answered' . ($surveyTitle ? " \"{$surveyTitle}\"." : ' the survey.'),
            self::link('admin.survey'),
            'survey',
            'survey_submitted'
        ));
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                           */
    /* ------------------------------------------------------------------ */

    /** Sab Admin users (users.role = 'Admin'). */
    public static function toAdmins(AppNotification $notification): void
    {
        try {
            $admins = User::where('role', 'Admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, $notification);
            }
        } catch (\Throwable $e) {
            Log::error('Admin notification failed: ' . $e->getMessage());
        }
    }

    /** Ek user ko (users.id). */
    public static function toUser($userId, AppNotification $notification): void
    {
        try {
            if ($userId && ($user = User::find($userId))) {
                $user->notify($notification);
            }
        } catch (\Throwable $e) {
            Log::error('User notification failed: ' . $e->getMessage());
        }
    }

    private static function link(string $key): ?string
    {
        foreach (self::ROUTES[$key] ?? [] as $name) {
            if (Route::has($name)) {
                return route($name);
            }
        }

        return null;
    }

    private static function productName(object $product): string
    {
        return Str::limit((string) ($product->name ?? $product->title ?? $product->product_name ?? 'your product'), 60);
    }

    private static function statusLabel($status): string
    {
        $s = strtolower(trim((string) $status));

        return match (true) {
            in_array($s, ['approved', 'approve', 'active', '1'], true)   => 'Approved',
            in_array($s, ['rejected', 'reject', 'declined', '2'], true) => 'Rejected',
            default                                                      => 'Pending',
        };
    }
}
