<?php

namespace App\Filament\Clusters\AdSettings\Resources\AdConditionResource\Pages;

use App\Filament\Clusters\AdSettings\Resources\AdConditionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAdConditions extends ManageRecords
{
    use ManageRecords\Concerns\Translatable;

    protected static string $resource = AdConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
