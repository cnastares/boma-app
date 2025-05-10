<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTrafficSource extends Model
{
    use HasFactory;

        protected $fillable = [
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_term',
            'utm_content',
            'traffic_source',
            'referrer_url',
            'full_url',
            'visitor_ip',
            'user_agent',
            'user_id',
            'location_data',
            'browser',
            'os',
            'device_name',
            'device_type',
        ];

    protected $casts=[
        'location_data'=>'array'
    ];

    public function trackable(){
        return $this->morphTo();
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
