<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\Ad;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        $recordId = request()->route('record');

        $restrictedIds = [1, 2, 3, 4];
        $shouldHide = in_array($recordId, $restrictedIds);

        return [
            Actions\DeleteAction::make()
            ->hidden($shouldHide),
        ];
    }
}
