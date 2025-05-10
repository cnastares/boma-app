<?php

namespace App\Filament\Clusters\Locations\Resources\Locations;

use App\Filament\Clusters\Locations;
use App\Filament\Clusters\Locations\Resources\Locations\StateResource\Pages;
use App\Filament\Clusters\Locations\Resources\Locations\StateResource\RelationManagers;
use App\Models\Country;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;



class StateResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = State::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 3;

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
        return userHasPermission('view_any_locations::state');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_locations::state');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_locations::state');
    }

    public static function canDelete($record): bool
    {
        return userHasPermission('delete_locations::state');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_locations::state');
    }

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_state');
    }
    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_state_list');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('country_id')
                    ->label(__('messages.t_ap_country'))
                    ->options(Country::all()->pluck('name', 'id')->toArray())
                    ->placeholder(__('messages.t_ap_select_country'))
                    ->required(),

                TextInput::make('name')
                    ->label(__('messages.t_ap_state_name'))
                    ->placeholder(__('messages.t_ap_enter_state_name'))
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
                Tables\Actions\DeleteAction::make()
                    ->before(function (State $record) {
                        $record->cities()->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($livewire) {
                            // Retrieve all selected state records
                            $selectedRecords = $livewire->getSelectedTableRecords();

                            // Perform deletion operations
                            foreach ($selectedRecords as $record) {
                                // Delete related cities
                                $record->cities()->delete();

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
            'index' => Pages\ManageStates::route('/'),
        ];
    }
}
