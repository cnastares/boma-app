<?php

namespace App\Filament\Resources\Settings\FooterSectionResource\Pages;

use App\Filament\Resources\Settings\FooterSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFooterSection extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = FooterSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    public function mutateFormDataBeforeSave(array $data): array
    {
        if($data['type'] ==='custom')
        {
            $data['predefined_identifier'] = null;
        }
        return $data;
     }
}
