<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use App\Models\Ad;
use App\Models\AdFieldValue;
use App\Models\City;
use App\Models\Field;
use App\Settings\AdSettings;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateAd extends CreateRecord
{
    protected static string $resource = AdResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['price_type_id'] = 1;
        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        if (isset($data['city_id'])) {
            $city = City::with('state', 'country')->find($data['city_id']);
            if ($city) {
                $data['city'] = $city->name;
                $data['latitude'] = $city->latitude;
                $data['longitude'] = $city->longitude;
                $data['location_name'] = $city->name;
                $data['location_display_name'] ="{$city->name}, {$city->state->name}, {$city->country->name}";
                $data['state'] = $city->state->name;
                $data['country'] = $city->country->name;
            }
        }
        $adSettings = app(AdSettings::class);
        $data['status'] = 'active';
        $data['posted_date'] = now();
        $data['user_id'] = auth()->id();
        $data['expires_at'] = now()->addDays($adSettings->ad_duration);
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Extract dynamic fields based on prefix and separate them from static data
        $dynamicFields = array_filter($data, function ($key) {
            return strpos($key, 'dynamic_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        // Remove dynamic fields from the main data array
        $data = array_diff_key($data, $dynamicFields);

        $model = static::getModel()::create($data);
        $this->updateAdSlug($model, $data['title']);
        // Save dynamic fields, remove 'dynamic_' prefix before saving
        foreach ($dynamicFields as $fieldName => $value) {
            $fieldId = substr($fieldName, 8); // Remove the first 8 characters 'dynamic_'
            $this->saveFieldValue($fieldId, $value, $model);
        }

        return $model;
    }

    protected function updateAdSlug(Ad $ad, $title)
    {
        $ad->slug = Str::slug(Str::limit($title, 138)) . '-' . substr($ad->id, 0, 8);
        $ad->save();
    }

    /**
     * Save field value to the database.
     */
    protected function saveFieldValue($name, $value, Ad $ad)
    {
        $fieldName = str_replace('data.', '', $name);
        $field = Field::find($fieldName);
        if (!$field) return;
        AdFieldValue::updateOrCreate(['ad_id' => $ad->id, 'field_id' => $field->id], ['value' => $value??'']);
    }

}
