<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id', 'package_category_id', 'package_promotion_id', 'quantity', 'price', 'offer_enabled', 'offer_price', 'offer_start', 'offer_end'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function category()
    {
        return $this->belongsTo(PackageCategory::class, 'package_category_id');
    }

    public function promotion()
    {
        return $this->belongsTo(PackagePromotion::class, 'package_promotion_id');
    }

}
