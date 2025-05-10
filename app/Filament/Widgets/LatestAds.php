<?php

namespace App\Filament\Widgets;

use App\Models\Ad;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class LatestAds extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'half';

    protected static ?int $sort = 4;  // Adjust the sort order as needed

    public static function canView(): bool
    {
        return Ad::latest()->count() > 0 && userHasPermission('widget_LatestAds');
    }

    public function table(Table $table): Table
    {
        return $table
        ->heading(__('messages.t_ap_latest_ads'))
        ->query(Ad::query()->latest('created_at'))
        ->defaultPaginationPageOption(5)
        ->defaultSort('created_at', 'desc')
        ->columns([
            SpatieMediaLibraryImageColumn::make('ads')
                ->collection('ads')
                ->conversion('thumb')
                ->defaultImageUrl(fn($record)=>getAdPlaceholderImage($record->id))
                ->label(__('messages.t_ap_ad_images'))
                ->size(40)
                ->circular()
                ->overlap(2)
                ->stacked()
                ->limit(3),

            TextColumn::make('title')
                ->label(__('messages.t_ap_title')),

            TextColumn::make('user.name')
                ->label(__('messages.t_ap_posted_by'))
                ->sortable(),

            TextColumn::make('price')
                ->label(__('messages.t_ap_price')),

            TextColumn::make('location_name')
                ->label(__('messages.t_ap_location')),

            TextColumn::make('posted_date') // Ensure your Ad model has 'posted_date'
                ->label(__('messages.t_ap_posted_on'))
                ->date(),

            TextColumn::make('category.name')
                ->label(__('messages.t_ap_category'))
                ->sortable(),

            SelectColumn::make('status')
                ->options([
                    'draft' => __('messages.t_ap_draft'),
                    'active' => __('messages.t_ap_active'),
                    'inactive' => __('messages.t_ap_inactive'),
                    'sold' => __('messages.t_ap_sold'),
                ])
                ->label(__('messages.t_ap_change_status')),
        ])
        ->actions([
            Action::make('view')
                ->icon('heroicon-o-eye')
                ->label(__('messages.t_ap_view_details'))
                ->url(fn (Ad $record): string => route('ad.overview', [
                    'slug' => $record->slug,
                    'admin_view' => 'true'
                ]))
                ->openUrlInNewTab()
        ]);
    }
}
