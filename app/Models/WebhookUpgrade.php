<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookUpgrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'payment_id',
        'payment_method',
        'status'
    ];
}
