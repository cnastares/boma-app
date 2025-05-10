<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = ['buyer_id', 'seller_id', 'experience', 'interaction', 'detail','rating'];

    public function likes()
    {
        return $this->hasMany(FeedbackLike::class);
    }

    public function replies()
    {
        return $this->hasMany(FeedbackReply::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
