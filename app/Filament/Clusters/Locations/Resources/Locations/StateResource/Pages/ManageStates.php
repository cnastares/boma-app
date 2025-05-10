<?php

namespace App\Filament\Clusters\Locations\Resources\Locations\StateResource\Pages;

use App\Filament\Clusters\Locations\Resources\Locations\StateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStates extends ManageRecords
{
    protected static string $resource = StateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
