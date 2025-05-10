<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // foreignUuid
        'ad_id', // foreignUuid
        'viewer_name',
        'viewer_phone',
        'viewer_email',
        'ad_price',
        'ad_url',
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

}
