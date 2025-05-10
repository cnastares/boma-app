<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'name', 'email', 'comment', 'ipAddress', 'user_agent', 'status'];

    public function blog()
    {
        return $this->belongsTo(BlogPost::class, 'post_id');
    }
}
