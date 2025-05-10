<?php

namespace App\Filament\Resources\Settings\LanguageResource\Pages;

use App\Filament\Resources\Settings\LanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLanguage extends EditRecord
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Refresh active langs
        fetch_active_languages(true);
    }
}
