<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class PostCategory extends Model
{
    use HasFactory, InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'name',
        'parent_id',
        'icon',
        'order',
        'slug',
        'description',
    ];

    public $translatable = [
        'name',
        'description'
    ];

    public function parent()
    {
        return $this->belongsTo(PostCategory::class, 'parent_id');
    }
    public function subcategories()
    {
        return $this->hasMany(PostCategory::class, 'parent_id');
    }

    public function BlogPosts()
    {
        return $this->hasMany(BlogPost::class);
    }

}
