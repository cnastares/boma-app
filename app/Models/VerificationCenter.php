<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VerificationCenter extends Model implements HasMedia
{
    use HasFactory,  InteractsWithMedia;

    protected $fillable = [
        'document_type',
        'status',
        'user_id',
        'declined_at',
        'verified_at',
        'comments'
    ];

      /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;


    // public function registerMediaConversions(Media $media = null): void
    // {
    //     $this->addMediaConversion('thumb')
    //          ->crop('crop-center', 400, 400);
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
