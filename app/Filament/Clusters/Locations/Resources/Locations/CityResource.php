<?php

namespace App\Filament\Clusters\Locations\Resources\Locations;

use App\Filament\Clusters\Locations;
use App\Filament\Clusters\Locations\Resources\Locations\CityResource\Pages;
use App\Filament\Clusters\Locations\Resources\Locations\CityResource\RelationManagers;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class CityResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = City::class;


    protected static ?string $cluster = Locations::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 4;

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
        return userHasPermission('view_any_locations::city');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_locations::city');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_locations::city');
    }

    public static function canDelete($record): bool
    {
        return userHasPermission('delete_locations::city');
    }
    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_locations::city');
    }

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_city');
    }
    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_city_list');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('country_id')
                    ->label(__('messages.t_ap_country'))
                    ->options(Country::orderBy('name')->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->placeholder(__('messages.t_ap_select_country'))
                    ->afterStateUpdated(fn(callable $set) => $set('state_id', null))
                    ->required(),

                Select::make('state_id')
                    ->label(__('messages.t_ap_state'))
                    ->options(function (Get $get) {
                        $countryId = $get('country_id');
                        if (!$countryId) {
                            return [];
                        }
                        return State::where('country_id', $countryId)->orderBy('name')->pluck('name', 'id')->toArray();
                    })
                    ->reactive()
                    ->placeholder(__('messages.t_ap_select_state'))
                    ->required(),

                TextInput::make('name')
                    ->label(__('messages.t_ap_city_name'))
                    ->placeholder(__('messages.t_ap_enter_city_name'))
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
                    ->required(),

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

                Tables\Columns\TextColumn::make('state.name')
                    ->label(__('messages.t_ap_state'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label(__('messages.t_ap_country'))
                    ->searchable(),

            ])
            ->filters([
                SelectFilter::make('country')
                    ->relationship('country', 'name')
                    ->label(__('messages.t_ap_country_filter'))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCities::route('/'),
        ];
    }
}
