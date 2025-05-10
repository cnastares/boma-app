<?php

namespace App\Filament\Resources\FieldResource\Pages;

use App\Filament\Resources\FieldResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateField extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = FieldResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
