<?php

namespace App\Filament\Resources\ContactAnalyticResource\Pages;

use App\Filament\Resources\ContactAnalyticResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactAnalytics extends ListRecords
{
    protected static string $resource = ContactAnalyticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
