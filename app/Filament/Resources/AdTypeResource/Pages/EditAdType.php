<?php

namespace App\Filament\Resources\AdTypeResource\Pages;

use App\Filament\Resources\AdTypeResource;
use App\Traits\AdType\HasLocationField;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdType extends EditRecord
{
    use HasLocationField;
    protected static string $resource = AdTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['location_details'])) {
            if (!isset($data['country_id'])) {
                $data['country_id'] = $data['location_details']['country_id'] ?? null;
            }
            if (!isset($data['state_id'])) {
                $data['state_id'] = $data['location_details']['state_id'] ?? null;
            }
            if (!isset($data['city_id'])) {
                $data['city_id'] = $data['location_details']['city_id'] ?? null;
            }
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        if(isset($data['marketplace']) && $data['marketplace'] == POINT_SYSTEM_MARKETPLACE){
            $data['enable_price']= true;
            $data['disable_price_type']= false;
            $data['customize_price_type']= false;
            $data['has_price_suffix']= false;
            $data['price_types']= [];
            $data['field_options']= [];
            $data['suffix_field_options']= [];
        }
        return $this->mutateLocationDetails($data);
    }
}
