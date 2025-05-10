<?php

namespace App\Models\Wallets;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'user_id',
        'bank_id',
        'account_number',
        'ifsc_code',
        'account_holder_name',
        'status',
        'is_default',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
