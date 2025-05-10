<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackReply extends Model
{
    use HasFactory;

    protected $fillable = ['feedback_id', 'user_id', 'reply'];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
