<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Survey extends Model
{
    public const ACTIVE = 1;
    public const INACTIVE = 0;

    protected $fillable = ['name', 'description', 'status'];

    protected $casts = [
        'status' => 'integer',
    ];

    /* ---------- Relations ---------- */

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('sort_order')->orderBy('id');
    }

    // Front par sirf Show wale question
    public function visibleQuestions()
    {
        return $this->questions()->where('status', 1);
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    /* ---------- Helpers ---------- */

    public function scopeActive($query)
    {
        return $query->where('status', self::ACTIVE);
    }

    public function getIsActiveAttribute(): bool
    {
        return (int) $this->status === self::ACTIVE;
    }

    /**
     * Abhi ka Active survey (front ke liye). Na ho to null.
     */
    public static function current(): ?self
    {
        return static::active()->latest('updated_at')->first();
    }

    /**
     * Is survey ko Active karo aur baaki sab Inactive. Ek time par sirf ek Active.
     */
    public function activate(): void
    {
        DB::transaction(function () {
            static::where('id', '!=', $this->id)->where('status', self::ACTIVE)->update(['status' => self::INACTIVE]);
            $this->update(['status' => self::ACTIVE]);
        });
    }
}
