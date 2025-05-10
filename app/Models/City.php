<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'state_id', 'state_code', 'country_id', 'country_code',
        'latitude', 'longitude', 'flag', 'wikiDataId',
    ];


    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * return State name for the city
     */
    public function getStateNameAttribute(){
        return $this->state?->name;
    }

    /**
     * Return country name for the city
     */
    public function getCountryNameAttribute(){
        return $this->country?->name;
    }
}
