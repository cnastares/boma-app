<?php

namespace App\Foundation\AdBase\Traits;

use App\Models\AdType;
use App\Models\Category;
use App\Models\City;

trait LocationFunctions
{
    public function updateLocation($ad, $value)
    {
        $adType = AdType::find($value);
        if ($adType) {
            if (!$adType->default_location && !$adType->disable_location) {
                $this->dispatch('enable-location');
            } else {
                $this->handleLocationBasedOnAdType($ad, $adType);
            }
        }
    }

    protected function handleLocationBasedOnAdType($ad, $adType)
    {
        if ($adType->default_location && $adType->location_details) {
            $this->updateLocationFromCityId($ad, $adType->location_details);
        }
        if ($adType->disable_location) {
            $this->dispatch('hide-location');
        }
    }

    protected function updateLocationFromCityId($ad, $locationDetails)
    {
        if (isset($locationDetails['city_id'])) {
            $cityDetail = City::with('state', 'country')->find($locationDetails['city_id']);
            if (!$cityDetail) {
                return;
            }
            $ad->update([
                'latitude' => $cityDetail->latitude,
                'longitude' => $cityDetail->longitude,
                'location_name' => $cityDetail->name . ' - ' . $cityDetail->state?->name,
                'location_display_name' => "{$cityDetail->name}, {$cityDetail->state?->name}, {$cityDetail->country->name}",
                'city' => $cityDetail->name,
                'state' => $cityDetail->state->name,
                'country' => $cityDetail->country->name,
            ]);
            $this->dispatch('hide-location');
        }
    }
}
