<?php

namespace App\Filament\Resources\Settings\LanguageResource\Pages;

use App\Filament\Resources\Settings\LanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Config;

class ListLanguages extends ListRecords
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        $isDemo = Config::get('app.demo');
        return [
            Actions\CreateAction::make()->hidden($isDemo),
        ];
    }
}
