<?php

namespace App\Models\Reservation;

use App\Models\User;
use App\Observers\Reservation\OrderStatusObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([OrderStatusObserver::class])]
class OrderStatusHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'order_id',
        'action',
        'command',
        'action_date',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
