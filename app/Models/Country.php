<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';

    public $timestamps = false;

    protected $fillable = [
        'name', 'iso3', 'numeric_code', 'iso2', 'phonecode', 'capital',
        'currency', 'currency_name', 'currency_symbol', 'tld', 'native',
        'region', 'region_id', 'subregion', 'subregion_id', 'nationality',
        'timezones', 'translations', 'latitude', 'longitude', 'emoji', 'emojiU',
        'flag', 'wikiDataId',
    ];

    public function states()
    {
        return $this->hasMany(State::class, 'country_id');
    }
    public function cities()
    {
        return $this->hasManyThrough(City::class, State::class, 'country_id', 'state_id');
    }

}
