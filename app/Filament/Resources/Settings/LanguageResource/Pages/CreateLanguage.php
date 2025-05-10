<?php

namespace App\Filament\Resources\Settings\LanguageResource\Pages;

use App\Filament\Resources\Settings\LanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\File;

class CreateLanguage extends CreateRecord
{
    protected static string $resource = LanguageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
       // Check if directory exists
        if (!File::exists(lang_path(strtolower($data['lang_code'])))) {

            // Create new folder
            File::makeDirectory(lang_path(strtolower($data['lang_code'])));

            // Copy translation file to new folder
            File::copy(lang_path('en/messages.php'), lang_path(strtolower($data['lang_code']) . "/messages.php"));

        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Refresh active langs
        fetch_active_languages(true);
    }
}



