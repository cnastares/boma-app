<?php

namespace App\Models\Reservation;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryOrder extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'items',
        'total_amount',
        'status',
        'shipping_address_id'
    ];

    protected $casts = [
        'items' => 'array'
    ];
}
