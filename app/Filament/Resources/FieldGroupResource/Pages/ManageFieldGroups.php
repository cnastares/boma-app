<?php

namespace App\Filament\Resources\FieldGroupResource\Pages;

use App\Filament\Resources\FieldGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFieldGroups extends ManageRecords
{
    use ManageRecords\Concerns\Translatable;
    protected static string $resource = FieldGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(), 
        ];
    }
}
