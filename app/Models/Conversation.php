<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Conversation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'ad_id',
        'buyer_id',
        'seller_id',
        'last_updated',
        'deleted_by_seller_at',
        'deleted_by_buyer_at',
    ];

    protected $dates = ['deleted_by_buyer_at', 'deleted_by_seller_at', 'last_updated'];


    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
