<?php

namespace App\Filament\App\Resources\MySaleResource\Pages;

use App\Filament\App\Resources\MySaleResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMySales extends ListRecords
{
    protected static string $resource = MySaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    // public function getTabs(): array
    // {
    //     $tabs = [];

    //     if (is_ecommerce_active()) {
    //         $tabs[RESERVATION_TYPE_RETAIL] = Tab::make()
    //             ->modifyQueryUsing(fn(Builder $query) => $query->where('order_type', RESERVATION_TYPE_RETAIL))
    //             ->label(__('messages.t_my_sale_tab_retail'));
    //     }

    //     if (isEnablePointSystem()) {
    //         $tabs[RESERVATION_TYPE_POINT_VAULT] = Tab::make()
    //             ->modifyQueryUsing(fn(Builder $query) => $query->where('order_type', RESERVATION_TYPE_POINT_VAULT))
    //             ->label(__('messages.t_my_sale_tab_point_vault'));
    //     }

    //     return $tabs;
    // }
}
