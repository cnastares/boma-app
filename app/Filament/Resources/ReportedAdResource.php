<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportedAdResource\Pages;
use App\Filament\Resources\ReportedAdResource\RelationManagers;
use App\Models\ReportedAd;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class ReportedAdResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ReportedAd::class;

    protected static ?int $navigationSort = 2;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'view',
            'delete_any',
            'view_detail'
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_reported::ad');
    }

    public static function canView($record): bool
    {
        return userHasPermission('view_reported::ad');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_reported::ad');
    }

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_reported_ad');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_core_management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('reason')
                    ->label(__('messages.t_ap_reason'))
                    ->content(fn(ReportedAd $record): string => $record->reason)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('ad'))
            ->columns([
                TextColumn::make('id')
                    ->label(__('messages.t_ap_report_id')),
                TextColumn::make('user.name')
                    ->label(__('messages.t_ap_reported_by')),
                TextColumn::make('reason')
                    ->label(__('messages.t_ap_reason'))
                    ->limit(30),
                TextColumn::make('created_at')
                    ->label(__('messages.t_ap_date_reported'))
                    ->date(),
                TextColumn::make('ad.user.name')
                    ->label(__('messages.t_ap_ad_owner')),
                SelectColumn::make('status')
                    ->options([
                        'pending' => __('messages.t_ap_pending'),
                        'seen' => __('messages.t_ap_seen'),
                    ])
                    ->label(__('messages.t_ap_change_status')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->label(__('messages.t_ap_view_ad_details'))
                    ->visible(fn () => userHasPermission('view_detail_reported::ad'))
                    ->url(fn(ReportedAd $record): string => route('ad.overview', [
                        'slug' => $record->ad?->slug,
                        'admin_view' => 'true'
                    ]))
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make()
                ->label(__('messages.t_ap_view_reason')),
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
            'index' => Pages\ManageReportedAds::route('/'),
        ];
    }
}
