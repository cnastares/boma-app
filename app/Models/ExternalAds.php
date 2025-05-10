<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalAds extends Model
{
    use HasFactory;
    protected $fillable=[
        'value',
        'ad_provider',
        'status'
    ];
}
