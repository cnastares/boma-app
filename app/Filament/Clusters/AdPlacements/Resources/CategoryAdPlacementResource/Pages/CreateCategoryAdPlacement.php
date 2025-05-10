<?php

namespace App\Filament\Clusters\AdPlacements\Resources\CategoryAdPlacementResource\Pages;

use App\Filament\Clusters\AdPlacements\Resources\CategoryAdPlacementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategoryAdPlacement extends CreateRecord
{
    protected static string $resource = CategoryAdPlacementResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
