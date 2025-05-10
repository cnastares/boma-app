<?php

namespace App\Filament\Resources\ReportedAdResource\Pages;

use App\Filament\Resources\ReportedAdResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageReportedAds extends ManageRecords
{
    protected static string $resource = ReportedAdResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
