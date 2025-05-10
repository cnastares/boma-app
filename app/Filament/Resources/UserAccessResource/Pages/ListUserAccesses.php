<?php

namespace App\Filament\Resources\UserAccessResource\Pages;

use App\Filament\Resources\UserAccessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserAccesses extends ListRecords
{
    protected static string $resource = UserAccessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
