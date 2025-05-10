<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class LatestRegisteredUsers extends BaseWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = 'half';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        // return User::where('is_admin', false)->count() > 0;
        return userHasPermission('widget_LatestRegisteredUsers');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('messages.t_ap_registered_users'))
            ->query(
                User::query()
                    ->where('is_admin', false)
                    ->latest('created_at')
            )
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('messages.t_ap_id')),

                TextColumn::make('name')
                    ->label(__('messages.t_ap_name')),

                TextColumn::make('email')
                    ->label(__('messages.t_ap_email')),

                TextColumn::make('phone_number')
                    ->label(__('messages.t_ap_phone_number')),

                TextColumn::make('created_at')
                    ->label(__('messages.t_ap_member_since'))
                    ->date(),

                ToggleColumn::make('email_verified_at')
                    ->label(__('messages.t_ap_email_verified'))
                    ->tooltip(__('messages.t_ap_email_verified_tooltip'))
                    ->updateStateUsing(function (User $record) {
                        $record->email_verified_at === null
                            ? $record->email_verified_at = Carbon::now()
                            : $record->email_verified_at = null;
                        $record->save();
                    }),

                TextColumn::make('ads_count')
                    ->counts('ads')
                    ->label(__('messages.t_ap_total_ads')),
            ]);
    }
}
