<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\SubscriptionManagement;
use App\Filament\Resources\CouponResource\Pages;
use App\Filament\Resources\CouponResource\RelationManagers;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class CouponResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Coupon::class;

    protected static ?string $cluster = SubscriptionManagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_coupon');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'create',
            'update',
            'view_any',
            'delete_any',
        ];
    }

    public static function canViewAny(): bool
    {
        return app('filament')->hasPlugin('subscription') && userHasPermission('view_any_coupon');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_coupon');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_coupon');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_coupon');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label(__('messages.t_ap_coupon_code'))
                    ->helperText(__('messages.t_ap_coupon_code_helper')),

                Forms\Components\Select::make('type')
                    ->options([
                        'fixed' => __('messages.t_ap_fixed_amount'),
                        'percentage' => __('messages.t_ap_percentage'),
                    ])
                    ->required()
                    ->label(__('messages.t_ap_discount_type'))
                    ->helperText(__('messages.t_ap_discount_type_helper')),

                Forms\Components\TextInput::make('discount_value')
                    ->numeric()
                    ->required()
                    ->label(__('messages.t_ap_discount_value'))
                    ->helperText(__('messages.t_ap_discount_value_helper')),

                Forms\Components\TextInput::make('usage_limit')
                    ->hidden()
                    ->numeric()
                    ->label(__('messages.t_ap_usage_limit'))
                    ->helperText(__('messages.t_ap_usage_limit_helper')),

                Forms\Components\DateTimePicker::make('expires_at')
                    ->label(__('messages.t_ap_expiration_date'))
                    ->required()
                    ->minDate(now())
                    ->nullable()
                    ->helperText(__('messages.t_ap_expiration_date_helper')),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('messages.t_ap_active'))
                    ->default(true)
                    ->helperText(__('messages.t_ap_is_active_helper')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('messages.t_ap_coupon_code'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('messages.t_ap_discount_type')),

                Tables\Columns\TextColumn::make('discount_value')
                    ->label(__('messages.t_ap_discount_value')),

                Tables\Columns\TextColumn::make('usage_limit')
                    ->label(__('messages.t_ap_usage_limit')),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label(__('messages.t_ap_active')),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label(__('messages.t_ap_expiration_date'))
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
