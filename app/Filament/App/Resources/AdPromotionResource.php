<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AdPromotionResource\Pages;
use App\Filament\App\Resources\AdPromotionResource\RelationManagers;
use App\Models\AdPromotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdPromotionResource extends Resource
{
    protected static ?string $model = AdPromotion::class;

    protected static ?int $navigationSort = 9;

    public static function getNavigationGroup(): ?string
    {
        return __('messages.t_ads_navigation');
    }
    public static function getNavigationLabel(): string
    {
        return __('messages.t_ad_promotions');
    }

    public static function getModelLabel(): string
    {
        return __('messages.t_ad_promotions');
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
        return $table->emptyStateIcon('/images/not-found.svg')
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('ad',function($query){
                return $query->where('user_id',auth()->id());
            }))
            ->emptyState(view('tables.empty-state', ['message' => __('messages.t_no_ad_promotions')]))
            ->columns([
                TextColumn::make('ad.title')
                ->label(__('messages.t_ad_name'))
                ->searchable(),

            TextColumn::make('promotion.name')
                ->label(__('messages.t_promotion_type'))
                ->searchable(),

            TextColumn::make('start_date')
                ->label(__('messages.t_promotion_start_date'))
                ->date(),

            TextColumn::make('end_date')
                ->label(__('messages.t_promotion_end_date'))
                ->date(),

            TextColumn::make('price')
                ->label(__('messages.t_price')),

            TextColumn::make('views')
                ->label(__('messages.t_views'))
                ->visible(function () {
                    if (getSubscriptionSetting('status')&& getActiveSubscriptionPlan()) {
                        return getActiveSubscriptionPlan()->boost_analysis;
                    }
                    return false;
                }),
                TextColumn::make('clicks')
                ->label(__('messages.t_clicks'))
                ->visible(function () {
                    if (getSubscriptionSetting('status')&& getActiveSubscriptionPlan()) {
                        return getActiveSubscriptionPlan()->boost_analysis;
                    }
                    return false;
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListAdPromotions::route('/'),
            // 'create' => Pages\CreateAdPromotion::route('/create'),
            // 'edit' => Pages\EditAdPromotion::route('/{record}/edit'),
        ];
    }
}
