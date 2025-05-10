<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'ad_promotion_id',
        'stripe_payment_id',
        'amount',
        'status',
    ];

    public function adPromotion()
    {
        return $this->belongsTo(AdPromotion::class);
    }

}
