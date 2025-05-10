<?php

namespace App\Filament\Clusters\Locations\Resources\Locations\CityResource\Pages;

use App\Filament\Clusters\Locations\Resources\Locations\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCities extends ManageRecords
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
