<?php

namespace App\Filament\Resources\PendingAdResource\Pages;

use App\Filament\Resources\PendingAdResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePendingAds extends ManageRecords
{
    protected static string $resource = PendingAdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
