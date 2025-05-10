<?php

namespace App\Models;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'promotion_id',
        'start_date',
        'end_date',
        'price',
        'order_package_item_id',
        'source',
        'subscription_id',
        'active',
        'views',
        'clicks'
    ];

    protected $casts = [
        'end_date' => 'datetime',
        'start_date' => 'datetime',
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function orderPackageItem()
    {
        return $this->belongsTo(OrderPackageItem::class, 'order_package_item_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeActive($query){
        $query->where('active',true);
    }
}
