<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\DynamicFields;
use App\Filament\Resources\FieldGroupResource\Pages;
use App\Filament\Resources\FieldGroupResource\RelationManagers;
use App\Models\FieldGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class FieldGroupResource extends Resource implements HasShieldPermissions
{
    use Translatable;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $model = FieldGroup::class;

    protected static ?string $cluster = DynamicFields::class;

    protected static ?int $navigationSort = 1;


    public static function getModelLabel(): string
    {
        return __('messages.t_ap_field_group');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any'
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_field::group');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_field::group');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_field::group');
    }

    public static function canDelete($record): bool
    {
        return userHasPermission('delete_field::group');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_field::group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('messages.t_ap_name'))
                    ->required()
                    ->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('order', 'asc'))
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('messages.t_ap_name'))
                    ->searchable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ManageFieldGroups::route('/'),
        ];
    }
}
