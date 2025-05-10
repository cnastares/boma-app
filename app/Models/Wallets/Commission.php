<?php

namespace App\Models\Wallets;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'user_id',
        'payable_type',  // Polymorphic model type
        'payable_id',    // Polymorphic model id
        'amount',
        'commission_rate',
        'commission_type',
        'commission_amount',
        'status',
    ];

    // Define the polymorphic relationship
    public function payable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
