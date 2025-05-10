<?php

namespace App\Filament\App\Resources\AdPromotionResource\Pages;

use App\Filament\App\Resources\AdPromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdPromotion extends EditRecord
{
    protected static string $resource = AdPromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
