<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\PromotionManagement;
use App\Filament\Resources\AdPromotionResource\Pages;
use App\Filament\Resources\AdPromotionResource\RelationManagers;
use App\Models\Ad;
use App\Models\AdPromotion;
use App\Models\Promotion;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class AdPromotionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = AdPromotion::class;

    protected static ?string $cluster = PromotionManagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_promoted_ad');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'update',
            'view_any',
            'delete_any',
        ];
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_ad::promotion');
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_ad::promotion');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_ad::promotion');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ad_id')
                ->label(__('messages.t_ap_ad_title'))
                ->options(Ad::all()->pluck('title', 'id')),
                Select::make('promotion_id')
                ->label(__('messages.t_ap_promotion_type'))
                ->options(Promotion::all()->pluck('name', 'id')),
                TextInput::make('price')
                ->label(__('messages.t_ap_price'))
                ->prefix(config('app.currency_symbol'))
                ->numeric(),
                DatePicker::make('start_date')->native(false)
                ->label(__('messages.t_ap_start_date')),
                DatePicker::make('end_date')->native(false)
                ->label(__('messages.t_ap_end_date'))

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->latest())
            ->columns([
                TextColumn::make('ad.title')
                    ->label(__('messages.t_ap_ad_title'))
                    ->searchable(),
                TextColumn::make('promotion.name')
                    ->label(__('messages.t_ap_promotion_type'))
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label(__('messages.t_ap_start_date'))
                    ->date(),
                TextColumn::make('end_date')
                ->label(__('messages.t_ap_end_date'))
                    ->date(),
                TextColumn::make('price')
                    ->label(__('messages.t_ap_price')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdPromotions::route('/'),
            'create' => Pages\CreateAdPromotion::route('/create'),
            'edit' => Pages\EditAdPromotion::route('/{record}/edit'),
        ];
    }
}
