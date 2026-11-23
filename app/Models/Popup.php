<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    protected $table = 'popup_master';

    protected $fillable = [
        'image',
        'title',
        'short_description',
        'btn_url',
        'status',
    ];
}
