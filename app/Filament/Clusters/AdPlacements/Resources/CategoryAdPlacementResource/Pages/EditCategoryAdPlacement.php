<?php

namespace App\Filament\Clusters\AdPlacements\Resources\CategoryAdPlacementResource\Pages;

use App\Filament\Clusters\AdPlacements\Resources\CategoryAdPlacementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoryAdPlacement extends EditRecord
{
    protected static string $resource = CategoryAdPlacementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
