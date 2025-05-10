<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderUpgrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_value',
        'subtotal_value',
        'taxes_value',
        'user_id',
        'status',
        'payment_method'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getAdTitleAttribute()
    {
        return $this->orderPromotions->first()?->adPromotion?->ad->title ?? null;
    }

    public function orderPromotions()
    {
        return $this->hasMany(OrderPromotion::class, 'order_upgrade_id');
    }
}
