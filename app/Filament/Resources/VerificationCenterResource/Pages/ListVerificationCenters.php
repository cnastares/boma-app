<?php

namespace App\Filament\Resources\VerificationCenterResource\Pages;

use App\Filament\Resources\VerificationCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVerificationCenters extends ListRecords
{
    protected static string $resource = VerificationCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
