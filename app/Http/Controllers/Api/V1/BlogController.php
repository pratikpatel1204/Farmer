<?php

namespace App\Http\Controllers\Api\V1;

use App\Support\ApiData;
use App\Support\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Blog list + post (blogs table). Sirf published (status 1, published_at aa chuka), delete nahi hua.
 */
class BlogController extends ApiController
{
    /** GET /blogs?q=&page= */
    public function index(Request $request)
    {
        if (! Market::hasBlogs()) {
            return $this->ok([], 'OK', 200, ['meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 9, 'total' => 0]]);
        }

        $q = trim((string) $request->query('q'));

        $blogs = self::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('description', 'like', $like));
            })
            ->orderByDesc('id')
            ->paginate($this->perPage($request, (int) config('kisanmandi.per_page.blogs', 9)))
            ->withQueryString();

        return $this->paginated($blogs, fn ($blog) => self::card($blog));
    }

    /** GET /blogs/{slug} (slug ya id) */
    public function show(string $slug)
    {
        if (! Market::hasBlogs()) {
            return $this->fail('Post not found.', 404);
        }

        $blog = self::query()->where('slug', $slug)->first()
            ?? (ctype_digit($slug) ? self::query()->where('id', (int) $slug)->first() : null);

        if (! $blog) {
            return $this->fail('Post not found.', 404);
        }

        if (self::has('views')) {
            DB::table('blogs')->where('id', $blog->id)->increment('views');
        }

        return $this->ok(self::card($blog) + [
            'description_html' => (string) ($blog->description ?? ''),
            'meta_title'       => $blog->meta_title ?? null,
            'meta_description' => $blog->meta_description ?? null,
            'latest'           => self::query()->where('id', '!=', $blog->id)->orderByDesc('id')->limit(4)->get()->map(fn ($b) => self::card($b)),
            'share_url'        => ApiData::webUrl('blog/' . ($blog->slug ?: $blog->id)),
        ]);
    }

    /** Home ke liye latest blogs */
    public static function cards(int $limit)
    {
        if (! Market::hasBlogs()) {
            return collect();
        }

        return self::query()->orderByDesc('id')->limit($limit)->get()->map(fn ($blog) => self::card($blog));
    }

    private static function query()
    {
        return DB::table('blogs')
            ->when(self::has('status'), fn ($q) => $q->where('status', 1))
            ->when(self::has('published_at'), fn ($q) => $q->where(fn ($w) => $w->whereNull('published_at')->orWhere('published_at', '<=', now())))
            ->when(self::has('deleted_at'), fn ($q) => $q->whereNull('deleted_at'));
    }

    private static function has(string $column): bool
    {
        static $cache = [];

        return $cache[$column] ??= Schema::hasColumn('blogs', $column);
    }

    private static function card($blog): array
    {
        $excerpt = $blog->short_description ?? null;
        if (! $excerpt) {
            $excerpt = \Illuminate\Support\Str::limit(ApiData::plainText($blog->description ?? ''), 160);
        }

        return [
            'id'           => $blog->id,
            'slug'         => $blog->slug ?: (string) $blog->id,
            'title'        => $blog->title,
            'excerpt'      => $excerpt,
            'image'        => ApiData::url($blog->image ?? null),
            'author_name'  => $blog->author_name ?? null,
            'is_featured'  => (bool) ($blog->is_featured ?? false),
            'views'        => (int) ($blog->views ?? 0),
            'published_at' => ApiData::date($blog->published_at ?? $blog->created_at ?? null),
        ];
    }
}
