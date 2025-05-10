<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\PromotionManagement;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\OrderUpgrade;
use App\Settings\PackageSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class TransactionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = OrderUpgrade::class;

    protected static ?string $cluster = PromotionManagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_order');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'delete'
        ];
    }


    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_transaction');
    }


    public static function canDelete($record): bool
    {
        return userHasPermission('delete_transaction');
    }
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query)
            ->columns([
                TextColumn::make('id')
                    ->label(__('messages.t_ap_order_id')),

                TextColumn::make('ad_title')
                    ->label(__('messages.t_ap_ad_name')),

                TextColumn::make('orderPromotions.adPromotion.promotion.name')
                    ->label(__('messages.t_ap_promotion_type')),

                TextColumn::make('payment_method')
                    ->label(__('messages.t_ap_payment_method')),

                TextColumn::make('taxes_value')
                    ->label(__('messages.t_ap_tax')),

                TextColumn::make('subtotal_value')
                    ->label(__('messages.t_ap_sub_total')),

                TextColumn::make('total_value')
                    ->label(__('messages.t_ap_total')),

                TextColumn::make('created_at')
                    ->label(__('messages.t_ap_date'))
                    ->date(),

                SelectColumn::make('status')
                    ->label(__('messages.t_ap_status'))
                    ->options([
                        'completed' => __('messages.t_ap_completed'),
                        'pending' => __('messages.t_ap_pending'),
                        'failed' => __('messages.t_ap_failed'),
                        'refunded' => __('messages.t_ap_refunded'),
                    ]),

            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return !app(PackageSettings::class)->status;
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
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}
