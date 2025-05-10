<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReview extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reviewable_id',
        'reviewable_type',
        'rating',
        'feedback',
        'is_verified',
        'user_id',
        'order_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
