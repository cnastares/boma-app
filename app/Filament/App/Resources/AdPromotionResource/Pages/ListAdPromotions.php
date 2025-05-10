<?php

namespace App\Filament\App\Resources\AdPromotionResource\Pages;

use App\Filament\App\Resources\AdPromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdPromotions extends ListRecords
{
    protected static string $resource = AdPromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
