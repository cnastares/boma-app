<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use App\Models\AdFieldValue;
use App\Models\Category;
use App\Models\City;
use App\Models\Field;
use App\Settings\AdSettings;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class EditAd extends EditRecord
{
    protected static string $resource = AdResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['category_id'])) {
            // Fetch the category using 'category_id'.
            $category = Category::find($data['category_id']);


            if ($category) {
                // If the category has a 'parent_id', it's a subcategory, so use the 'parent_id' as the 'main_category_id'.
                // Otherwise, it's a main category, and use its 'id' as the 'main_category_id'.
                $data['main_category_id'] = $category->parent_id ?? $category->id;
            }

        }
        $city = $this->findNearestCity($data['latitude'], $data['longitude']);
        if ($city) {
            $data['city_id'] = $city->id;
            $data['state_id'] = $city->state_id;
            $data['country_id'] = $city->country_id;
        }

        // Check if image_properties is null
        // Adjust the logic to work with image_limit instead of actual image count
        $image_properties = [];

        // Determine the loop limit based on the smaller of images count or image limit
        $loopLimit = count($this->record->images());
        for ($index = 0; $index < $loopLimit; $index++) {
            $imgIndex = $index + 1;
            if (!isset($this->record->image_properties["{$imgIndex}"])) {
                $data['image_properties']["{$imgIndex}"] = $this->record->title;
            } else {
                $data['image_properties']["{$imgIndex}"] = $this->record->image_properties["{$imgIndex}"];
            }
        }

        // Check if there's an 'ad_id' to fetch dynamic fields
        if (isset($data['id'])) {
            $savedValues = AdFieldValue::where('ad_id', $data['id'])
                ->pluck('value', 'field_id')
                ->mapWithKeys(function ($value, $fieldId) {
                    // Assuming the value is stored as JSON
                    return [$fieldId => $value];
                });

            // Populate data array with dynamic field values
            foreach ($savedValues as $fieldId => $value) {
                $data['dynamic_' . $fieldId] = $value; // Assign each dynamic value to its respective field in the form data array
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['city_id'])) {
            $city = City::with('state', 'country')->find($data['city_id']);
            if ($city) {
                $data['city'] = $city->name;
                $data['latitude'] = $city->latitude;
                $data['longitude'] = $city->longitude;
                $data['location_name'] = $city->name;
                $data['location_display_name'] = "{$city->name}, {$city->state->name}, {$city->country->name}";
                $data['state'] = $city->state->name;
                $data['country'] = $city->country->name;
            }
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extract dynamic fields from the data
        $dynamicFields = array_filter($data, function ($key) {
            return strpos($key, 'dynamic_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        // Remove dynamic fields from the main data array
        $data = array_diff_key($data, $dynamicFields);

        // Process dynamic fields after extracting them
        if (!empty($dynamicFields)) {
            $this->saveDynamicFields($dynamicFields, $record->id);
        }

        $record->update($data);

        return $record;
    }

    protected function saveDynamicFields($dynamicFields, $adId)
    {
        foreach ($dynamicFields as $key => $value) {
            $fieldId = substr($key, 8); // Remove 'dynamic_' prefix
            if ($value) {
                $this->saveFieldValue($fieldId, $value, $adId);
            }
        }
    }

    /**
     * Save field value to the database.
     */
    protected function saveFieldValue($fieldId, $value, $adId)
    {
        $field = Field::find($fieldId);
        if (!$field)
            return null; // Ensure the field exists
        $adFieldValue = AdFieldValue::updateOrCreate(
            ['ad_id' => $adId, 'field_id' => $field->id],
            ['value' => $value]
        );
    }


    public function findNearestCity($latitude, $longitude)
    {
        if (empty($latitude) || empty($longitude)) {
            return null;
        }
        return City::with('state', 'country')
            ->select('cities.*', DB::raw("(6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance"))
            ->orderBy('distance', 'asc')
            ->first();

    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
