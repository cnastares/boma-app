<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAdPosting extends Model
{
    use HasFactory;

    protected $casts = [
        'period_start' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'ad_count',
        'free_ad_count',
        'period_start'
    ];
}
