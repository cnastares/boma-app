<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class UnVerifiedUsers extends BaseWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        // return User::where('is_admin', false)->count() > 0;
        return userHasPermission('widget_UnVerifiedUsers');
    }

    public static function isDiscovered(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('messages.t_ap_unverify_users'))
            ->query(
                User::whereDoesntHave('verification')
                    ->orWhereHas('verification', function ($query) {
                        $query->where('status', 'pending'); // Removed `get()`
                    })
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
                TextColumn::make('verification.status')
                    ->label(__('messages.t_ap_status'))
                    ->badge()
                    ->default(__('messages.t_not_verified'))
                    ->formatStateUsing(fn(User $record) => $record->verification?->status ?? __('messages.t_not_verified'))
                    ->color(fn($state) => match ($state) {
                        strtolower(__('messages.t_pending_status')) => 'warning',
                        __('messages.t_not_verified')=> 'danger',
                    }),
            ]);
    }
}
