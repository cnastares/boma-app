<?php

namespace App\Filament\App\Resources\MySaleResource\Pages;

use App\Filament\App\Resources\MySaleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMySale extends EditRecord
{
    protected static string $resource = MySaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
