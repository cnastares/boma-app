<?php

namespace App\Filament\Resources\AdPromotionResource\Pages;

use App\Filament\Resources\AdPromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdPromotions extends ListRecords
{
    protected static string $resource = AdPromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
