<?php

namespace App\Filament\Clusters\AdSettings\Resources;

use App\Filament\Clusters\AdSettings;
use App\Filament\Clusters\AdSettings\Resources\AdConditionResource\Pages;
use App\Filament\Clusters\AdSettings\Resources\AdConditionResource\RelationManagers;
use App\Models\AdCondition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class AdConditionResource extends Resource implements HasShieldPermissions
{
    use Translatable;

    protected static ?string $model = AdCondition::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $cluster = AdSettings::class;

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_ad_condition');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'update',
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_ad::condition');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_ad::condition');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('messages.t_ap_name'))
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(__('messages.t_ap_name'))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAdConditions::route('/'),
        ];
    }
}
