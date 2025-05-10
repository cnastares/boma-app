<?php

namespace App\Models\Reservation;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Reservation\Order;
use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Concerns\HasUuid;



class RefundTransaction extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia,HasUuids;

    protected $fillable = [
        'order_id',
        'user_id',
        'type',
        'description',
        'status'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function histories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

}
