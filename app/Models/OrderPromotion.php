<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_upgrade_id',
        'ad_promotion_id'
    ];

    // Relationships
    public function orderUpgrade()
    {
        return $this->belongsTo(OrderUpgrade::class, 'order_upgrade_id');
    }

    public function adPromotion()
    {
        return $this->belongsTo(AdPromotion::class, 'ad_promotion_id');
    }


}
