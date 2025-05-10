<?php

namespace App\Filament\Resources\Settings\FooterSectionResource\Pages;

use App\Filament\Resources\Settings\FooterSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFooterSection extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = FooterSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // ...
        ];
    }
}
