<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'stripe_coupon_id',
        'type',
        'discount_value',
        'usage_limit',
        'expires_at',
        'is_active',
    ];

    protected $dates = ['expires_at'];

    // Check if the coupon is expired
    public function isExpired()
    {
        return $this->active && (!$this->expires_at || Carbon::now()->lt($this->expires_at));
    }

    // Check if the coupon can still be used
    public function canBeUsed()
    {
        return $this->is_active && !$this->isExpired();
    }
}
