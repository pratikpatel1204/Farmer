<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInquiry extends Model
{
    use HasFactory;

    protected $table = 'product_inquiries';

    protected $fillable = [
        'product_id',
        'member_id',
        'user_id',
        'full_name',
        'email',
        'phone',
        'message',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Inquiry belongs to Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Inquiry belongs to Seller (member who owns the product)
    // Table me column member_id hai, seller_id nahi
    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    // Purane code ke liye alias
    public function seller()
    {
        return $this->member();
    }

    // Inquiry belongs to Customer/User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
