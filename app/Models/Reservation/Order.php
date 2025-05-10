<?php

namespace App\Models\Reservation;

use App\Traits\HasCommission;
use App\Models\CustomerReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;


class Order extends Model
{
    use HasFactory, HasUuids, HasCommission;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'order_number',
        'order_date',
        'total_amount',
        'discount_amount',
        'subtotal_amount',
        'tax_amount',
        'converted_amount',
        'exchange_rate',
        'payment_method',
        'order_status',
        'shipping_tracking_number',
        'shipping_carrier',
        'shipping_address',
        'payment_status',
        'transaction_id',
        'contact_name',
        'contact_phone_number',
        'order_type',
        'points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function refundTransactions()
    {
        return $this->hasMany(RefundTransaction::class);
    }

    public function buyerRefundTransactions()
    {
        return $this->hasMany(RefundTransaction::class)->where('type', 'buyer');
    }

    public function sellerRefundTransactions()
    {
        return $this->hasMany(RefundTransaction::class)->where('type', 'seller');
    }


    public function customerReviews()
    {
        return $this->hasMany(CustomerReview::class);
    }
}
