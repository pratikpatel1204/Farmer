<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'role',
        'unique_id',
        'sponsor_id',
        'sponsor_name',
        'email',
        'password',
        'temp_password',
        'mobile',
        'mobile_otp',
        'mobile_verified',
        'mobile_verified_at',
        'state',
        'city',
        'area',
        'pincode',
        'address',
        'address_source',
        'full_name',
        'status',
        'profile_image',
    ];

    protected $hidden = [
        'password',
        'temp_password',
        'remember_token',
        'mobile_otp',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'mobile_verified' => 'boolean',
            'mobile_verified_at' => 'datetime',
            'status' => 'integer',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'member_id');
    }

    public function states()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }

    public function citys()
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }
}
