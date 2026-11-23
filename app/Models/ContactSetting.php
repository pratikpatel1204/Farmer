<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $table = 'contact_settings';

    protected $fillable = [
        'phone',
        'whatsapp_number',
        'whatsapp_message',
        'email',
        'office_address',
        'working_hours',
        'map_embed_url',
    ];

    /**
     * Single-row settings: hamesha pehli row lo, na ho to bana do.
     */
    public static function current(): self
    {
        return static::first() ?? static::create([]);
    }

    /** tel: link ke liye sirf + aur digits */
    public function getPhoneLinkAttribute(): ?string
    {
        return $this->phone ? 'tel:' . preg_replace('/[^0-9+]/', '', $this->phone) : null;
    }

    /** wa.me link ke liye sirf digits */
    public function getWhatsappLinkAttribute(): ?string
    {
        return $this->whatsapp_number
            ? 'https://wa.me/' . preg_replace('/\D/', '', $this->whatsapp_number)
            : null;
    }

    public function getEmailLinkAttribute(): ?string
    {
        return $this->email ? 'mailto:' . $this->email : null;
    }
}
