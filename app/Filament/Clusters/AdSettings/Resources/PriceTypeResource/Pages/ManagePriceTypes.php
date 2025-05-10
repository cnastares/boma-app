<?php

namespace App\Filament\Clusters\AdSettings\Resources\PriceTypeResource\Pages;

use App\Filament\Clusters\AdSettings\Resources\PriceTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePriceTypes extends ManageRecords
{
    use ManageRecords\Concerns\Translatable;

    protected static string $resource = PriceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
