<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StoreBanner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
   
    protected $fillable = [
        'name',
        'image',
        'order',
        'alternative_text',
        'link',
        'user_id'
    ];

    public function getBannerImageAttribute()
    {
        return $this->getFirstMedia('user_banner_images');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('responsive-banner')
            ->quality(100)
            ->withResponsiveImages()
            ->nonQueued();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
