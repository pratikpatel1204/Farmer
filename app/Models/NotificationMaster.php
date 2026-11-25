<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Admin ke Notification Master se bane notifications (sab members ke liye same).
 * Default (is_default) wala member panel me sabse upar pinned rehta hai.
 */
class NotificationMaster extends Model
{
    protected $table = 'notification_master';

    protected $fillable = ['title', 'description', 'is_default', 'status'];

    protected $casts = ['is_default' => 'boolean'];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'Active');
    }

    /** Member panel ka order: pinned (default) sabse upar, baaki latest pehle. */
    public function scopeForMembers(Builder $q): Builder
    {
        return $q->active()->orderByDesc('is_default')->latest('id');
    }

    /** Abhi pinned (default + active) notification, nahi hai to null. */
    public static function pinned(): ?self
    {
        return static::active()->where('is_default', true)->latest('id')->first();
    }

    /** Is notification ko default banao aur baaki sab ka default hata do (ek time par ek hi pinned). */
    public function makeDefault(): void
    {
        static::where('id', '!=', $this->id)->where('is_default', true)->update(['is_default' => false]);
        $this->is_default = true;
        $this->save();
    }

    /**
     * CKEditor ka HTML save karne se pehle saaf karo: sirf formatting tags rehne do,
     * script/style/iframe aur on*="" / javascript: hata do.
     */
    public static function cleanHtml(?string $html): string
    {
        $allowed = '<p><br><strong><b><em><i><u><s><a><ul><ol><li><h2><h3><h4><blockquote>'
            . '<table><thead><tbody><tr><th><td><figure><span>';

        $html = preg_replace('#<(script|style|iframe)[^>]*>.*?</\1>#is', '', (string) $html);
        $html = strip_tags($html, $allowed);
        $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/(href|src)\s*=\s*(["\']?)\s*javascript:[^"\'>\s]*\2/i', '$1="#"', $html);

        return trim($html);
    }
}
