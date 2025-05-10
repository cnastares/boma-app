<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'order',
        'alternative_text',
        'link',
        'country_id',
        'state_id',
        'city_id',
        'html',
        'banner_type'
    ];

    public function bannerAnalytics(){
        return $this->hasMany(BannerAnalytics::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
