<?php

namespace App\Models;

use App\Observers\MessageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(MessageObserver::class)]
class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'ad_id',
        'sender_id',
        'receiver_id',
        'content',
        'is_read',
        'conversation_id',
        'attachment',
        'is_audio',
        'seen',
        'is_negotiable_conversation',
        'is_accept_offer'
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

}
