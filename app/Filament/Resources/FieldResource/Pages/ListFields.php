<?php

namespace App\Filament\Resources\FieldResource\Pages;

use App\Filament\Resources\FieldResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFields extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    protected static string $resource = FieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make()
        ];
    }
    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->label(__('messages.t_ap_tab_all')),
            'default' => Tab::make()
                ->label(__('messages.t_ap_tab_default'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('default', true)),
            'custom' => Tab::make()
                ->label(__('messages.t_ap_tab_custom'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('default', false)),
        ];
    }
}
