<?php

namespace App\Filament\Resources\AdTypeResource\Pages;

use App\Filament\Resources\AdTypeResource;
use App\Traits\AdType\HasLocationField;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAdType extends CreateRecord
{
    use HasLocationField;
    protected static string $resource = AdTypeResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->mutateLocationDetails($data);
    }
}
