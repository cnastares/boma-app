<?php

namespace App\Filament\Clusters\Locations\Resources\Locations;

use App\Filament\Clusters\Locations;
use App\Filament\Clusters\Locations\Resources\Locations\CountryResource\Pages;
use App\Filament\Clusters\Locations\Resources\Locations\CountryResource\RelationManagers;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class CountryResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Country::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $cluster = Locations::class;

    public static function getPermissionPrefixes(): array
    {
        return [
            'create',
            'update',
            'view_any',
            'delete',
            'delete_any'
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_locations::country');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_locations::country');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_locations::country');
    }

    public static function canDelete($record): bool
    {
        return userHasPermission('delete_locations::country');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_locations::country');
    }

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_country');
    }
    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_country_list');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->label(__('messages.t_ap_country_name'))
                ->placeholder(__('messages.t_ap_enter_country_name'))
                ->required(),

            TextInput::make('iso2')
                ->alpha()
                ->maxLength(2)
                ->label(__('messages.t_ap_short_name'))
                ->placeholder(__('messages.t_ap_enter_country_short_name'))
                ->unique(ignoreRecord:true)
                ->required(),

            TextInput::make('latitude')
                ->label(__('messages.t_ap_latitude'))
                ->placeholder(__('messages.t_ap_enter_latitude'))
                ->numeric()
                ->required(),

            TextInput::make('longitude')
                ->label(__('messages.t_ap_longitude'))
                ->placeholder(__('messages.t_ap_enter_longitude'))
                ->numeric()
                ->required()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(__('messages.t_ap_name'))
                ->searchable(),
                Tables\Columns\TextColumn::make('iso2')
                    ->label(__('messages.t_ap_code'))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->before(function (Country $record) {
                    $record->cities()->delete();
                    $record->states()->delete();
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($livewire) {
                        // Retrieve all selected Country records
                        $selectedRecords = $livewire->getSelectedTableRecords();

                        // Perform deletion operations
                        foreach ($selectedRecords as $record) {
                            // Delete related cities
                            $record->cities()->delete();

                            // Delete related states
                            $record->states()->delete();

                            // Now delete the country itself
                            $record->delete();
                        }
                     }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCountries::route('/'),
        ];
    }
}
