<?php

namespace App\Filament\Resources\Settings\FooterSectionResource\Pages;

use App\Filament\Resources\Settings\FooterSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFooterSections extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = FooterSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
             Actions\LocaleSwitcher::make(),
        ];
    }
}
