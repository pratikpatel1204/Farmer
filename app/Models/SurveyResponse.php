<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    // Pehle submit ke baad kitne ghante tak answer badal sakte hain
    public const EDIT_HOURS = 24;

    protected $fillable = ['survey_id', 'user_id', 'submitted_at', 'locked_at'];

    protected $casts = [
        'submitted_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    /**
     * 24 ghante ke andar hai to true (answer badal sakta hai).
     */
    public function canEdit(): bool
    {
        return $this->locked_at && now()->lt($this->locked_at);
    }
}
