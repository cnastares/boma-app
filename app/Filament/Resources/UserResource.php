<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\UserManagement;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ToggleColumn;
use Carbon\Carbon;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneInputColumn;
use Illuminate\Support\HtmlString;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;



class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = UserManagement::class;

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_user');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_user_accounts');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'create',
            'view_any',
            'delete_any',
            'delete',
            'force_delete',
            'force_delete_any',
            'restore',
            'restore_any',
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_user');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_user');
    }

    public static function canDelete($record): bool
    {
        return userHasPermission('delete_user');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_user');
    }

    public static function canForceDelete($record): bool
    {
        return userHasPermission('force_delete_user');
    }

    public static function canForceDeleteAny(): bool
    {
        return userHasPermission('force_delete_any_user');
    }

    public static function canRestore($record): bool
    {
        return userHasPermission('restore_user');
    }

    public static function canRestoreAny(): bool
    {
        return userHasPermission('restore_any_user');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('is_admin', false);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('messages.t_ap_user_id')),

                TextColumn::make('name')
                    ->label(__('messages.t_ap_user_name'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('messages.t_ap_user_email'))
                    ->searchable(),

                PhoneInputColumn::make('phone_number')
                    ->label(__('messages.t_ap_user_phone_number'))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('messages.t_ap_user_created_at'))
                    ->date(),

                ToggleColumn::make('suspended')
                    ->label(__('messages.t_ap_user_suspended')),

                ToggleColumn::make('email_verified_at')
                    ->label(__('messages.t_ap_user_email_verified'))
                    ->tooltip(__('messages.t_ap_user_email_verified_tooltip'))
                    ->updateStateUsing(function (User $record) {
                        $record->email_verified_at === null
                            ? $record->email_verified_at = Carbon::now()
                            : $record->email_verified_at = null;
                        $record->save();
                    }),

                TextColumn::make('ads_count')
                    ->counts('ads')
                    ->label(__('messages.t_ap_user_ads_count')),

                TextColumn::make('dynamic_fields_list')
                    ->label(__('messages.t_ap_user_details'))
                    ->separator(',')
                    ->listWithLineBreaks()
                    ->expandableLimitedList()
                    ->limitList(1),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
