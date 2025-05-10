<?php

namespace App\Filament\Clusters\AdPlacements\Resources\CategoryAdPlacementResource\Pages;

use App\Filament\Clusters\AdPlacements\Resources\CategoryAdPlacementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoryAdPlacements extends ListRecords
{
    protected static string $resource = CategoryAdPlacementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
