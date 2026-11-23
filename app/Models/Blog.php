<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Blog extends Model
{
    use SoftDeletes;

    protected $table = 'blogs';

    protected $fillable = [
        'title',
        'slug',
        'image',
        'short_description',
        'description',
        'author_name',
        'meta_title',
        'meta_description',
        'is_featured',
        'status',
        'published_at',
        'views',
        'created_by',
    ];

    protected $casts = [
        'is_featured'  => 'boolean',
        'status'       => 'boolean',
        'published_at' => 'datetime',
    ];

    /** Only published blogs whose publish date has arrived (for the frontend) */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    /** Generates a unique slug (ignores its own ID while editing) */
    public static function makeUniqueSlug(string $text, ?int $ignoreId = null): string
    {
        $base = Str::slug($text) ?: 'blog';
        $slug = $base;
        $i = 1;

        while (static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function getUrlAttribute(): string
    {
        return url('blog/' . $this->slug);
    }

    public function getDisplayDateAttribute(): string
    {
        return ($this->published_at ?? $this->created_at ?? now())->format('d M Y');
    }

    public function getReadMinutesAttribute(): int
    {
        $words = str_word_count(strip_tags($this->description ?? ''));
        return max(1, (int) ceil($words / 200));
    }

    /** Short text for listing / meta */
    public function getExcerptAttribute(): string
    {
        return $this->short_description
            ?: Str::limit(trim(strip_tags($this->description ?? '')), 160);
    }
}
