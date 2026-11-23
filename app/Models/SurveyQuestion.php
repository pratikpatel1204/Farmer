<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    // Ek question me kitne option ho sakte hain (migration me option_1..option_6)
    public const MAX_OPTIONS = 6;

    protected $fillable = [
        'survey_id', 'question',
        'option_1', 'option_2', 'option_3', 'option_4', 'option_5', 'option_6',
        'sort_order', 'status',
    ];

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    /**
     * Sirf bhare hue option: [option_no => text]. null / khali option skip.
     * Jaise option_1 = Yes, option_2 = null, option_3 = No  ->  [1 => 'Yes', 3 => 'No']
     */
    public function getOptionsAttribute(): array
    {
        $options = [];
        for ($i = 1; $i <= self::MAX_OPTIONS; $i++) {
            $text = trim((string) $this->{'option_' . $i});
            if ($text !== '') {
                $options[$i] = $text;
            }
        }

        return $options;
    }

    /**
     * Question ka HTML (CKEditor). Script, iframe, onclick jaisi cheezein hata kar.
     */
    public function getQuestionHtmlAttribute(): string
    {
        $allowed = '<p><br><strong><b><em><i><u><s><ul><ol><li><h2><h3><h4><blockquote><a><span><figure><img><table><thead><tbody><tr><th><td>';
        $html = strip_tags((string) $this->question, $allowed);
        $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html);

        return $html;
    }

    /**
     * Home page ke chhote card ke liye plain text.
     */
    public function getQuestionTextAttribute(): string
    {
        $text = html_entity_decode(strip_tags(str_replace(['</p>', '<br>', '<br/>', '<br />'], ' ', (string) $this->question)), ENT_QUOTES, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text));
    }
}
