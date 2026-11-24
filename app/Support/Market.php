<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sab front pages ki common product query aur category helper.
 * Sirf SELECT query hai, database me kuch change nahi hota.
 *
 * I WANT / I HAVE:
 *   products.listing_type ('want' / 'have') column ho to wahi use hota hai.
 *   Column na ho to purana tareeka: Farmer ka product = I Have, Non Farmer ka = I Want.
 */
class Market
{
    /** Approved products + active owner. Bank / Aadhaar / PAN / password columns kabhi select nahi hote. */
    public static function products()
    {
        return DB::table('products')
            ->join('users', 'users.id', '=', 'products.member_id')
            ->where('products.status', 'Approved')
            // Deal complete (member ne "Completed" kiya) wale products website par nahi dikhenge
            ->whereNull('products.completed_at')
            ->where('users.status', 1)
            ->whereNull('users.deleted_at')
            ->select([
                'products.id',
                'products.title',
                'products.slug',
                'products.product_tags',
                'products.main_image',
                'products.category',
                'products.created_at',
                'users.full_name as owner_name',
                'users.area as owner_area',
                'users.role as owner_role',
            ]);
    }

    private static ?bool $hasListingType = null;

    public static function hasListingType(): bool
    {
        return self::$hasListingType ??= Schema::hasColumn('products', 'listing_type');
    }

    /** $type = 'want' | 'have' */
    public static function whereType($query, string $type)
    {
        if (self::hasListingType()) {
            return $query->where('products.listing_type', $type);
        }

        return $query->where('users.role', $type === 'have' ? 'Farmer' : 'Non Farmer');
    }

    /** Eloquent Product ho ya DB row, dono par chalta hai */
    public static function typeOf($product): string
    {
        $type = data_get($product, 'listing_type');
        if (in_array($type, ['want', 'have'], true)) {
            return $type;
        }
        $role = data_get($product, 'owner_role') ?? data_get($product, 'member.role');

        return $role === 'Farmer' ? 'have' : 'want';
    }

    /** Search box: title, tags, category */
    public static function search($query, ?string $q)
    {
        $q = trim((string) $q);
        if ($q === '') {
            return $query;
        }

        return $query->where(function ($w) use ($q) {
            $w->where('products.title', 'like', "%{$q}%")
                ->orWhere('products.product_tags', 'like', "%{$q}%")
                ->orWhere('products.category', 'like', "%{$q}%");
        });
    }

    public static function sort($query, ?string $sort)
    {
        return match ($sort) {
            'oldest' => $query->orderBy('products.id'),
            'name'   => $query->orderBy('products.title'),
            default  => $query->orderByDesc('products.id'),
        };
    }

    // ---------------- Categories (config/kisanmandi.php) ----------------

    public static function categories(): array
    {
        return config('kisanmandi.categories', []);
    }

    public static function category(string $slug): ?array
    {
        $cat = self::categories()[$slug] ?? null;

        return $cat ? $cat + ['slug' => $slug] : null;
    }

    private static array $valueCache = [];

    /**
     * products.category me ye sab values is category ki maani jayengi.
     * products.category me categories table ki id save hoti hai, isliye naam se id bhi nikal lete hain.
     */
    public static function categoryValues(string $slug): array
    {
        if (isset(self::$valueCache[$slug])) {
            return self::$valueCache[$slug];
        }

        $cat = self::categories()[$slug] ?? [];
        $values = array_values(array_unique(array_filter(array_merge(
            [$slug, $cat['name'] ?? null],
            $cat['match'] ?? []
        ))));

        if ($values && Schema::hasTable('categories') && Schema::hasColumn('categories', 'name')) {
            $ids = DB::table('categories')->whereIn('name', $values)->pluck('id')->map(fn($id) => (string) $id)->all();
            $values = array_values(array_unique(array_merge($values, $ids)));
        }

        return self::$valueCache[$slug] = array_map('strval', $values);
    }

    public static function whereCategory($query, ?string $slug)
    {
        if (! $slug || ! self::category($slug)) {
            return $query;
        }

        return $query->whereIn('products.category', self::categoryValues($slug));
    }

    /** products.category ki value se config wali category (icon, color ke liye) */
    public static function categoryFor($value): ?array
    {
        foreach (self::categories() as $slug => $cat) {
            if (in_array((string) $value, self::categoryValues($slug), true)) {
                return $cat + ['slug' => $slug];
            }
        }

        return null;
    }

    /** Har category me kitne product: ['vegetables' => 12, ...]. $type do to sirf want/have gine. */
    public static function categoryCounts(?string $type = null): array
    {
        $query = self::products()->reorder();
        if ($type) {
            self::whereType($query, $type);
        }

        $rows = $query->select('products.category', DB::raw('COUNT(*) as total'))
            ->groupBy('products.category')
            ->pluck('total', 'category');

        $counts = [];
        foreach (self::categories() as $slug => $cat) {
            $counts[$slug] = 0;
            foreach (self::categoryValues($slug) as $value) {
                $counts[$slug] += (int) ($rows[$value] ?? 0);
            }
        }

        return $counts;
    }

    // ---------------- Eloquent: Product + categories table ----------------

    /** Approved + not completed product + active member (home, listing, category, enquiry sab jagah same) */
    public static function productQuery()
    {
        return \App\Models\Product::query()
            ->where('products.status', 'Approved')
            // Deal complete (member ne "Completed" kiya) wale products website par nahi dikhenge
            ->whereNull('products.completed_at')
            ->whereHas('member', function ($query) {
                $query->where('status', 1)->whereNull('deleted_at');
            });
    }

    /** products.category column ka naam (Product::categorys relation se) */
    public static function categoryKey(): string
    {
        return (new \App\Models\Product)->categorys()->getForeignKeyName();
    }

    /** Category model (Product::categorys relation se, class ka naam likhne ki zarurat nahi) */
    public static function categoryModel()
    {
        return (new \App\Models\Product)->categorys()->getRelated();
    }

    private static $tableCategories = null;

    /** categories table ki saari active categories (naam ke hisaab se), ek request me ek hi query */
    public static function tableCategories()
    {
        if (self::$tableCategories !== null) {
            return self::$tableCategories;
        }

        $model = self::categoryModel();
        $query = $model->newQuery();
        // status column ho to sirf active (1 / Active) categories
        if (Schema::hasColumn($model->getTable(), 'status')) {
            $query->whereIn('status', [1, '1', 'Active', 'active']);
        }

        return self::$tableCategories = $query->orderBy('name')->get();
    }

    /** URL wala slug: model me slug (column ya accessor) ho to wahi, warna naam se */
    public static function slugOf($category): string
    {
        $slug = $category->slug ?? null;

        return $slug ?: (\Illuminate\Support\Str::slug($category->name ?? '') ?: (string) $category->getKey());
    }

    /** slug (ya id) se category dhundo */
    public static function findCategory(string $slug)
    {
        return self::tableCategories()->first(
            fn($category) => self::slugOf($category) === $slug || (string) $category->getKey() === $slug
        );
    }

    /** Category ki image ka URL (image na ho to null, tab icon dikhega) */
    public static function categoryImage($category): ?string
    {
        $image = trim((string) ($category->image ?? ''));
        if ($image === '') {
            return null;
        }
        if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        // "/uploads/..." ya "public/uploads/..." bhi chalega
        $image = ltrim($image, '/');
        if (\Illuminate\Support\Str::startsWith($image, 'public/')) {
            $image = substr($image, 7);
        }

        // Sirf file ka naam save ho to uploads/categories/ folder maana hai
        return asset(\Illuminate\Support\Str::contains($image, '/') ? $image : 'uploads/categories/' . $image);
    }

    /**
     * Product card / detail ki image ka URL.
     * Product ki main image ho to wahi. People Want (want) product me image na ho to uski category ki image.
     * Kuch na mile to null (tab icon dikhega). Eloquent Product aur DB row dono par chalta hai.
     */
    public static function productImage($product): ?string
    {
        $main = trim((string) data_get($product, 'main_image', ''));
        if ($main !== '') {
            return \Illuminate\Support\Str::startsWith($main, ['http://', 'https://']) ? $main : asset($main);
        }

        if (self::typeOf($product) !== 'want') {
            return null;
        }

        $categoryId = (string) data_get($product, 'category', '');
        $category = $categoryId === ''
            ? null
            : self::tableCategories()->first(fn($c) => (string) $c->getKey() === $categoryId);

        return $category ? self::categoryImage($category) : null;
    }

    /** Icon + rang: config/kisanmandi.php me naam mile to wahan se, warna default */
    public static function categoryLook($category): array
    {
        $cat = self::categoryFor($category->name ?? null);

        return ['icon' => $cat['icon'] ?? 'fa-seedling', 'color' => $cat['color'] ?? 'green'];
    }

    /** Har category id me kitne product: [3 => 12, ...]. $type do to sirf want/have. */
    public static function countsByCategoryId(?string $type = null)
    {
        $key = self::categoryKey();

        return self::productQuery()
            ->when($type, fn($query) => $query->where('listing_type', $type))
            ->selectRaw("{$key} as category_id, COUNT(*) as total")
            ->groupBy($key)
            ->pluck('total', 'category_id');
    }

    // ---------------- Images ----------------

    /** sub_images / company_images: JSON array ho, do baar encode hua JSON ho, ya comma se alag, sab chalega */
    public static function imageList($value): array
    {
        $list = $value;
        // "[\"uploads\/a.jpg\"]" jaisa double-encoded JSON bhi khul jaye
        for ($i = 0; $i < 3 && is_string($list); $i++) {
            $decoded = json_decode($list, true);
            if ($decoded === null) {
                break;
            }
            $list = $decoded;
        }
        if (is_string($list)) {
            $list = explode(',', $list);
        }
        if (! is_array($list)) {
            return [];
        }

        return array_values(array_filter(array_map(fn($v) => trim((string) $v, " \t\n\r\"'[]"), $list)));
    }

    /** Profile ke social links (alag columns): sirf http/https wale */
    public static function profileLinks($profile): array
    {
        $links = [];
        foreach (['website', 'facebook', 'instagram', 'youtube', 'linkedin'] as $key) {
            $url = trim((string) ($profile->{$key} ?? ''));
            if ($url !== '' && ! preg_match('#^https?://#i', $url) && $key === 'website') {
                $url = 'https://' . $url;
            }
            if (preg_match('#^https?://[^\s]+$#i', $url)) {
                $links[$key] = $url;
            }
        }

        return $links;
    }

    /**
     * Member ka likha HTML (profile description) safe tarike se: sirf simple tags, koi attribute nahi.
     * Script, style, onclick, link jaisi cheezein hat jaati hain.
     */
    public static function safeHtml(?string $html): string
    {
        $html = preg_replace('#<(script|style|iframe|object)[^>]*>.*?</\1>#is', '', (string) $html);
        $html = strip_tags($html, '<p><br><h2><h3><h4><h5><strong><b><em><i><u><ul><ol><li><blockquote>');

        return trim(preg_replace('#<([a-z][a-z0-9]*)\b[^>]*>#i', '<$1>', $html));
    }

    /** City / State: relation se naam, warna column me naam likha ho (id na ho) to wahi */
    public static function placeName($related, $raw): ?string
    {
        if (! empty($related?->name)) {
            return $related->name;
        }

        return ($raw && ! is_numeric($raw)) ? $raw : null;
    }

    /** Blog table hai ya nahi (nahi hai to page khali message dikhayega, error nahi) */
    public static function hasBlogs(): bool
    {
        return Schema::hasTable('blogs');
    }
}