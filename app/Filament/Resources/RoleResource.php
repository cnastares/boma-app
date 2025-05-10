<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Forms\ShieldSelectAllToggle;
use App\Filament\Resources\RoleResource\Pages;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class RoleResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }


    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_role');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_roles_permissions');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_role');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_role');
    }

    public static function canDelete($record): bool
    {
        return userHasPermission('delete_role');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_role');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_user_access');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('messages.t_ap_name'))
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('guard_name')
                                    ->label(__('messages.t_ap_guard_name'))
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->nullable()
                                    ->maxLength(255),

                                Forms\Components\Select::make(config('permission.column_names.team_foreign_key'))
                                    ->label(__('messages.t_ap_team'))
                                    ->placeholder(__('messages.t_ap_team_placeholder'))
                                    /** @phpstan-ignore-next-line */
                                    ->default([Filament::getTenant()?->id])
                                    ->options(fn(): Arrayable => Utils::getTenantModel() ? Utils::getTenantModel()::pluck('name', 'id') : collect())
                                    ->hidden(fn(): bool => !(static::shield()->isCentralApp() && Utils::isTenancyEnabled()))
                                    ->dehydrated(fn(): bool => !(static::shield()->isCentralApp() && Utils::isTenancyEnabled())),

                                ShieldSelectAllToggle::make('select_all')
                                    ->onIcon('heroicon-s-shield-check')
                                    ->offIcon('heroicon-s-shield-exclamation')
                                    ->label(__('messages.t_ap_select_all_name'))
                                    ->helperText(fn(): HtmlString => new HtmlString(__('messages.t_ap_select_all_message')))
                                    ->dehydrated(fn(bool $state): bool => $state),

                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ]),
                    ]),
                static::getShieldFormComponents(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('font-medium')
                    ->label(__('messages.t_ap_name'))
                    ->formatStateUsing(fn($state): string => Str::headline($state))
                    ->searchable(),

                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->color('warning')
                    ->label(__('messages.t_ap_guard_name')),

                Tables\Columns\TextColumn::make('team.name')
                    ->default(__('messages.t_ap_global'))
                    ->badge()
                    ->color(fn(mixed $state): string => str($state)->contains(__('messages.t_ap_global')) ? 'gray' : 'primary')
                    ->label(__('messages.t_ap_team'))
                    ->searchable()
                    ->visible(fn(): bool => static::shield()->isCentralApp() && Utils::isTenancyEnabled()),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('messages.t_ap_permissions'))
                    ->counts('permissions')
                    ->colors(['success']),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('messages.t_ap_updated_at'))
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
    ->before(function ($record, Tables\Actions\DeleteAction $action) {
        if ($record->users->count() > 0) {
            Notification::make()
                ->title(__('messages.t_error'))
                ->body(__('messages.t_ap_role_in_use'))
                ->danger()
                ->send();

            $action->cancel();
        }
    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                ->before(function ($records, Tables\Actions\DeleteBulkAction $action) {
                    $assignedRoles = $records->filter(fn($record) => $record->users->count() > 0);

                    if ($assignedRoles->isNotEmpty()) {
                        Notification::make()
                            ->title(__('messages.t_error'))
                            ->body(__('messages.t_ap_some_roles_in_use'))
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                })
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getCluster(): ?string
    {
        return Utils::getResourceCluster() ?? static::$cluster;
    }

    public static function getModel(): string
    {
        return Utils::getRoleModel();
    }

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_role');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Utils::isResourceNavigationRegistered();
    }

    // public static function getNavigationLabel(): string
    // {
    //     return __('filament-shield::filament-shield.nav.role.label');
    // }

    // public static function getNavigationIcon(): string
    // {
    //     return __('filament-shield::filament-shield.nav.role.icon');
    // }

    public static function getNavigationSort(): ?int
    {
        return Utils::getResourceNavigationSort();
    }

    public static function getSlug(): string
    {
        return Utils::getResourceSlug();
    }

    public static function getNavigationBadge(): ?string
    {
        return Utils::isResourceNavigationBadgeEnabled()
            ? strval(static::getEloquentQuery()->count())
            : null;
    }

    public static function isScopedToTenant(): bool
    {
        return Utils::isScopedToTenant();
    }

    public static function canGloballySearch(): bool
    {
        return Utils::isResourceGloballySearchable() && count(static::getGloballySearchableAttributes()) && static::canViewAny();
    }
}
