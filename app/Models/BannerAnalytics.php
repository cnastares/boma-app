<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerAnalytics extends Model
{
    use HasFactory;
    protected $fillable=[
        'banner_id',
        'event',
        'ip_address'
    ];

    public function banner(){
        return $this->belongsTo(Banner::class);
    }
}
