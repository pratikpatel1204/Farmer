<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'establishment_year',
        'number_of_workers',
        'area_in_square_yard',
        'website',
        'youtube',
        'facebook',
        'instagram',
        'linkedin',
        'bank_name',
        'account_number',
        'branch_name',
        'ifsc_code',
        'aadhar_image',
        'personal_pan_image',
        'aadhar_number',
        'pan_number',
        'company_images',
        'company_video',
        'status',
        'reject_reason',
    ];

    protected $casts = [
        'company_images' => 'array',
        'establishment_year' => 'integer',
        'number_of_workers' => 'integer',
        'area_in_square_yard' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
