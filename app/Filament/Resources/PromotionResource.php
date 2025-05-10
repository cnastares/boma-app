<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\PromotionManagement;
use App\Filament\Resources\PromotionResource\Pages;
use App\Filament\Resources\PromotionResource\RelationManagers;
use App\Models\Promotion;
use App\Settings\PackageSettings;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class PromotionResource extends Resource implements HasShieldPermissions
{
    use Translatable;

    protected static ?string $cluster = PromotionManagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $model = Promotion::class;

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_promotion');
    }
    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_promotion');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'update',
            'view_any',
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_promotion');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_promotion');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_promotion');
    }

    public static function canDelete($record): bool
    {
        return userHasPermission('delete_promotion');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('messages.t_ap_promotion_name')),
                TextInput::make('description')
                    ->label(__('messages.t_ap_description')),
                TextInput::make('duration')
                    ->suffix(__('messages.t_ap_days'))
                    ->label(__('messages.t_ap_duration'))
                    ->visible(!app(PackageSettings::class)->status)
                    ->numeric(),
                TextInput::make('price')
                    ->label(__('messages.t_ap_price'))
                    ->prefix(config('app.currency_symbol'))
                    ->visible(!app(PackageSettings::class)->status)
                    ->numeric(),
                ColorPicker::make('background_color')
                    ->label(__('messages.t_ap_background_color'))
                    ->visible(fn($record) => $record ? ($record->id == 1 || $record->id == 3) : false),
                ColorPicker::make('text_color')
                    ->label(__('messages.t_ap_text_color'))
                    ->visible(fn($record) => $record ? ($record->id == 1 || $record->id == 3) : false),
                Toggle::make('is_enabled')
                    ->visible(fn($record) => $record ? ($record->id == 4) : false)
                    ->label(__('messages.t_ap_is_enabled'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.t_ap_promotion_name')),

                TextColumn::make('description')
                    ->label(__('messages.t_ap_description')),

                TextColumn::make('duration')
                    ->suffix(__('messages.t_ap_days'))
                    ->visible(!app(PackageSettings::class)->status)
                    ->label(__('messages.t_ap_duration')),

                TextColumn::make('price')
                    ->prefix(config('app.currency_symbol'))
                    ->visible(!app(PackageSettings::class)->status)
                    ->label(__('messages.t_ap_price'))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPromotions::route('/'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}
