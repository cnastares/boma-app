<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPackage extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'transaction_id', 'payment_method', 'total_value', 'subtotal_value', 'taxes_value', 'user_id', 'status'
    ];

    public function packageItems()
    {
        return $this->hasMany(OrderPackageItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
