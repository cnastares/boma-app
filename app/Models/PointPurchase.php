<?php

namespace App\Models;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointPurchase extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'kp_amount',
        'point_purchases',
        'status',
        'email',
        'phone_number',
        'name',
        'surname',
        'address_line',
        'city_id',
        'state_id',
        'country_id',
        'postal_code',
        'transaction_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'kp_amount' => 'integer',
        'point_purchases' => 'integer',
        'city_id' => 'integer',
        'state_id' => 'integer',
        'country_id' => 'integer',
    ];

    /**
     * Get the user associated with the point vault.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the city associated with the point vault.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the state associated with the point vault.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the country associated with the point vault.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
