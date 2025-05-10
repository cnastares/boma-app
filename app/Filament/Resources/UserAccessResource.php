<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\UserManagement;
use App\Filament\Resources\UserAccessResource\Pages;
use App\Filament\Resources\UserAccessResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class UserAccessResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = UserManagement::class;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_staff_management');
    }

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_staff');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'create',
            'view_any',
            'update',
            'delete_any'
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_user::access');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_user::access');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_user::access');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_user::access');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->required()
                ->unique(ignoreRecord: true),
                Select::make('role')
                    ->required()
                    ->relationship('roles')
                    ->options(Role::pluck('name', 'id'))
                    ->searchable()
                    ->live(onBlur: true)
                    ->preload()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            // ->modifyQueryUsing(fn(Builder $query) => $query->where('is_admin', true))
            ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles'))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
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
            'index' => Pages\ListUserAccesses::route('/'),
            'create' => Pages\CreateUserAccess::route('/create'),
            'edit' => Pages\EditUserAccess::route('/{record}/edit'),
        ];
    }
}
