<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AdImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'index',
        'image_path',
        'is_primary',
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function getImagePathAttribute($value)
    {
        $storage_type = config('filesystems.default');

        if ($storage_type === 'local') {
            // Check if the file exists in local storage, if not return default image
            return asset('storage/' . $value);
        } else {
            return Storage::disk('s3')->url($value);
        }

        // If none of the above conditions are met, return a default image URL
        return asset('images/placeholder.jpg');
    }

}
