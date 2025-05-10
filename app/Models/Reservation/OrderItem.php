<?php

namespace App\Models\Reservation;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'order_id',
        'ad_id',
        'quantity',
        'price',
        'discount_price',
        'total_price',
        'points',
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class, 'ad_id', 'id');
    }
}
