<?php

namespace App\Traits\AdType;

trait HasLocationField {
    
    protected function mutateLocationDetails($data)
    {
        $locationDetails = [];
        foreach ($data as $key => $value) {
            $locationKeys = ['country_id', 'state_id', 'city_id'];
            if (in_array($key, $locationKeys)) {
                $locationDetails[$key] = $value;
            }
        }
        $data['location_details'] = $locationDetails;
        return $data;
    }
}
