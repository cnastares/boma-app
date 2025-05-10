<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPackageItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id','order_package_id', 'package_item_id', 'duration', 'name', 'activation_date', 'expiry_date', 'purchased', 'available', 'used', 'type', 'price', 'promotion_id'
    ];

    protected $dates = ['activation_date', 'expiry_date'];

    public function orderPackage()
    {
        return $this->belongsTo(OrderPackage::class);
    }

    public function packageItem()
    {
        return $this->belongsTo(PackageItem::class);
    }

    public function usedPackageItems()
    {
        return $this->hasMany(UsedPackageItem::class);
    }

    public function adPromotion()
    {
        return $this->hasOne(AdPromotion::class, 'order_package_item_id');
    }
}
