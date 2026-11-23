<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * App API ke JSON objects (ProductCard, Category, MemberCard, Me ...).
 * Ek hi jagah se banne se har API me same shape milta hai.
 *
 * Bank, Aadhaar, PAN, KYC, password, OTP kabhi yahan se bahar nahi jaate.
 * Public objects me member ka mobile / email bhi nahi.
 */
class ApiData
{
    /** Product list ke liye relations (N+1 query se bachne ke liye) */
    public const PRODUCT_WITH = ['member.states', 'member.citys', 'categorys'];

    /** Notification type -> app ka screen */
    public const NOTIFICATION_SCREENS = [
        'product_inquiry' => 'inquiries',
        'product_status'  => 'my_products',
        'profile_status'  => 'business_profile',
        'welcome'         => 'business_profile',
    ];

    public const INQUIRY_STATUS = [0 => 'Pending', 1 => 'Contacted', 2 => 'Closed'];

    /** DB path -> full URL. Khali ho to null. */
    public static function url(?string $path): ?string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $path = ltrim($path, '/');
        if (Str::startsWith($path, 'public/')) {
            $path = substr($path, 7);
        }

        return asset($path);
    }

    public static function urls($value): array
    {
        return array_values(array_filter(array_map([self::class, 'url'], Market::imageList($value))));
    }

    public static function date($value): ?string
    {
        return $value ? \Illuminate\Support\Carbon::parse($value)->toIso8601String() : null;
    }

    public static function webUrl(string $path): string
    {
        return url($path);
    }

    /** "Ludhiana, Punjab" (city/state id ho to relation se naam) */
    public static function place($user): ?string
    {
        if (! $user) {
            return null;
        }

        $parts = array_filter([
            Market::placeName($user->citys ?? null, $user->city ?? null),
            Market::placeName($user->states ?? null, $user->state ?? null),
        ]);

        return $parts ? implode(', ', $parts) : null;
    }

    public static function initials(?string $name): string
    {
        $words = preg_split('/\s+/', trim((string) $name)) ?: [];
        $letters = '';
        foreach (array_slice(array_filter($words), 0, 2) as $word) {
            $letters .= mb_strtoupper(mb_substr($word, 0, 1));
        }

        return $letters ?: 'KM';
    }

    /** Member ka likha text: HTML hata kar, line break rakh kar */
    public static function plainText(?string $html): string
    {
        $text = preg_replace('#<br\s*/?>|</p>|</li>|</h[1-6]>#i', "\n", (string) $html);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');

        return trim(preg_replace("/\n{3,}/", "\n\n", $text));
    }

    public static function category($category, $counts = null): ?array
    {
        if (! $category) {
            return null;
        }

        $look = Market::categoryLook($category);
        $data = [
            'id'    => $category->getKey(),
            'name'  => $category->name,
            'slug'  => Market::slugOf($category),
            'image' => Market::categoryImage($category),
            'icon'  => $look['icon'],
            'tint'  => $look['color'],
        ];

        if ($counts !== null) {
            $data['counts'] = $counts;
        }

        return $data;
    }

    /** Product card (list, home, related). $product = Eloquent Product with PRODUCT_WITH */
    public static function productCard($product): array
    {
        $member = $product->member;

        return [
            'id'        => $product->id,
            'slug'      => $product->slug ?: (string) $product->id,
            'title'     => $product->title,
            'type'      => Market::typeOf($product),
            'image'     => self::url($product->main_image),
            'category'  => self::category($product->categorys),
            'owner'     => [
                'name'      => $member->full_name ?? null,
                'unique_id' => $member->unique_id ?? null,
                'role'      => $member->role ?? null,
                'location'  => self::place($member),
                'area'      => $member->area ?? null,
            ],
            'posted_at' => self::date($product->created_at),
        ];
    }

    /** Public member card. $user = Eloquent User with profile, states, citys */
    public static function memberCard($user): array
    {
        return [
            'unique_id' => $user->unique_id,
            'name'      => $user->full_name,
            'role'      => $user->role,
            'image'     => self::url($user->profile_image),
            'initials'  => self::initials($user->full_name),
            'verified'  => ($user->profile->status ?? null) === 'Approved',
            'city'      => Market::placeName($user->citys ?? null, $user->city),
            'state'     => Market::placeName($user->states ?? null, $user->state),
            'area'      => $user->area,
        ];
    }

    /** Login user khud (sirf apni details, bank / KYC nahi) */
    public static function me($user): array
    {
        $user->loadMissing(['profile', 'states', 'citys']);

        return [
            'id'                   => $user->id,
            'unique_id'            => $user->unique_id,
            'role'                 => $user->role,
            'full_name'            => $user->full_name,
            'initials'             => self::initials($user->full_name),
            'mobile'               => $user->mobile,
            'email'                => $user->email,
            'image'                => self::url($user->profile_image),
            'state'                => ['id' => is_numeric($user->state) ? (int) $user->state : null, 'name' => Market::placeName($user->states, $user->state)],
            'city'                 => ['id' => is_numeric($user->city) ? (int) $user->city : null, 'name' => Market::placeName($user->citys, $user->city)],
            'area'                 => $user->area,
            'pincode'              => $user->pincode,
            'address'              => $user->address,
            'sponsor_id'           => $user->sponsor_id,
            'sponsor_name'         => $user->sponsor_name,
            'profile_status'       => $user->profile->status ?? null,
            'unread_notifications' => $user->unreadNotifications()->count(),
            'referral_link'        => self::referralLink($user),
            'public_profile_url'   => self::webUrl('profile/' . $user->unique_id),
            'joined_at'            => self::date($user->created_at),
        ];
    }

    public static function referralLink($user): string
    {
        $base = \Illuminate\Support\Facades\Route::has('refer') ? route('refer') : url('register');

        return $base . '?sponsor_id=' . urlencode((string) $user->unique_id);
    }

    public static function notification($notification): array
    {
        $data = $notification->data ?? [];
        $type = $data['type'] ?? 'general';

        return [
            'id'         => $notification->id,
            'type'       => $type,
            'title'      => $data['title'] ?? 'Notification',
            'message'    => $data['message'] ?? '',
            'icon'       => $data['icon'] ?? 'bell',
            'read'       => $notification->read_at !== null,
            'created_at' => self::date($notification->created_at),
            'target'     => ['screen' => self::NOTIFICATION_SCREENS[$type] ?? 'notifications'],
        ];
    }

    public static function inquiry($inquiry): array
    {
        $status = (int) $inquiry->status;

        return [
            'id'           => $inquiry->id,
            'full_name'    => $inquiry->full_name,
            'phone'        => $inquiry->phone,
            'email'        => $inquiry->email,
            'message'      => $inquiry->message,
            'status'       => $status,
            'status_label' => self::INQUIRY_STATUS[$status] ?? 'Pending',
            'created_at'   => self::date($inquiry->created_at),
        ];
    }
}
