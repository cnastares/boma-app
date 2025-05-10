<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Seo extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = [
        'meta_title',
        'favicon',
        'meta_description',
        'meta_keywords',
    ];

    protected $appends=[
        'ogImage'
    ];
    protected $casts = [
        'meta_keywords' => 'array',
    ];
    public function seoable()
    {
        $this->morphTo();
    }
    public function getOgImageAttribute()
    {
        $imageUrl = $this->getFirstMediaUrl('seo');
        // Check if the image URL is not empty
        if (!empty($imageUrl)) {
            return $imageUrl;
        }
        return null;
    }
}
