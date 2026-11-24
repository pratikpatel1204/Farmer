<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'member_id',
        'sku',
        'title',
        'slug',
        'product_tags',
        'main_image',
        'sub_images',
        'category',
        'sub_category_id',
        'listing_type',
        'description',
        'brochure',
        'status',
        'completed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        /*
        |--------------------------------------------------------------------------
        | Creating
        |--------------------------------------------------------------------------
        */

        static::creating(function ($product) {

            $product->slug = static::generateUniqueSlug(
                $product->title
            );

            if (empty($product->status)) {
                $product->status = 'Pending';
            }

            if (empty($product->listing_type)) {
                $product->listing_type = 'have';
            }
        });


        /*
        |--------------------------------------------------------------------------
        | Updating
        |--------------------------------------------------------------------------
        */

        static::updating(function ($product) {

            if ($product->isDirty('title')) {

                $product->slug = static::generateUniqueSlug(
                    $product->title,
                    $product->id
                );
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Unique Slug
    |--------------------------------------------------------------------------
    */

    protected static function generateUniqueSlug($title, $exceptId = null)
    {
        $slug = Str::slug($title);

        if (empty($slug)) {
            $slug = 'product';
        }

        $originalSlug = $slug;
        $count = 1;

        while (
            static::where('slug', $slug)
            ->when($exceptId, function ($query) use ($exceptId) {
                $query->where('id', '!=', $exceptId);
            })
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    // Always returns an array (handles JSON string, double-encoded JSON, null)
    protected function subImages(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $images = is_array($value) ? $value : json_decode($value ?? '[]', true);
                if (is_string($images)) {
                    $images = json_decode($images, true); // double-encoded JSON
                }

                return is_array($images) ? array_values(array_filter($images)) : [];
            }
        );
    }

    // Seller
    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    // Category
    public function categorys()
    {
        return $this->belongsTo(Category::class, 'category');
    }

    // Sub Category (optional)
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    // Product Attributes
    public function ProductAttrs()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'id');
    }
    public function attributesList()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id');
    }
}
