<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'payment_method',
        'status',
        'data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
