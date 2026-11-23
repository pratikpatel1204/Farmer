<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'image',
        'meta_tag',
        'meta_description',
    ];

    // public function products()
    // {
    //     return $this->hasMany(Product::class, 'category', 'id');   // pehle se hai
    // }


    // Category.php and Subcategory.php
    public function getSlugAttribute()
    {
        return \Illuminate\Support\Str::slug($this->name);
    }
}
