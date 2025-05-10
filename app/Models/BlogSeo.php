<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogSeo extends Model
{
    use HasFactory;
    protected $table = 'blog_seo';
    protected $fillable = ['post_id', 'title', 'description'];

    public function blog()
    {
        return $this->belongsTo(BlogPost::class);
    }
    
}
