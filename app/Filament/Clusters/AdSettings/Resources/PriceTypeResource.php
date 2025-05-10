<?php

namespace App\Filament\Clusters\AdSettings\Resources;

use App\Filament\Clusters\AdSettings;
use App\Filament\Clusters\AdSettings\Resources\PriceTypeResource\Pages;
use App\Filament\Clusters\AdSettings\Resources\PriceTypeResource\RelationManagers;
use App\Models\PriceType;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class PriceTypeResource extends Resource implements HasShieldPermissions
{
    use Translatable;

    protected static ?string $model = PriceType::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $cluster = AdSettings::class;

    protected static ?int $navigationSort = 3;

    public static function getPermissionPrefixes(): array
    {
        return [
            'update',
            'view_any',
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_price::type');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_price::type');
    }
    public static function getModelLabel(): string
    {
        return __('messages.t_ap_price_type');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('name')
                ->label(__('messages.t_ap_name'))
                    ->required()
                    ->maxLength(65535),
                TextInput::make('label')
                ->label(__('messages.t_ap_label'))
                    ->hidden(fn($record, $operation) => ($operation == 'edit' && $record->id == 1 ? true : false))
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(__('messages.t_ap_name')),
                Tables\Columns\TextColumn::make('label')
                ->label(__('messages.t_ap_label'))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make()->hidden(fn($record) => in_array($record->id, [1, 2, 3, 4]))

            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePriceTypes::route('/'),
        ];
    }
}
