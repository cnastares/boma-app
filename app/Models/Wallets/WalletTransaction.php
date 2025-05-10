<?php

namespace App\Models\Wallets;

use App\Models\User;
use App\Observers\WalletTransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([WalletTransactionObserver::class])]
class WalletTransaction extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'wallet_id',
        'user_id',
        'amount',
        'points',
        'is_added',
        'transaction_type',
        'transaction_reference',
        'status',
        'bank_details',
        'payable_type',  // Polymorphic model type
        'payable_id',    // Polymorphic model id
    ];

    protected $casts = [
        'bank_details' => 'array'
    ];

    public function payable()
    {
        return $this->morphTo();
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
