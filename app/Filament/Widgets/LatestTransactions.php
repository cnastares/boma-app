<?php

namespace App\Filament\Widgets;

use App\Models\OrderUpgrade;
use App\Models\OrderPackageItem;
use App\Settings\PackageSettings;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class LatestTransactions extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 6;  // Adjust the sort order as needed

    public static function canView(): bool
    {
        // return true; // Logic to determine if the widget should be viewable
        return userHasPermission('widget_LatestTransactions');
    }

    public function table(Table $table): Table
    {
        if (app(PackageSettings::class)->status) {
            // Configuration for OrderPackageItem
            return $table
            ->defaultSort('created_at', 'desc')
            ->heading(__('messages.t_ap__latest_transactions'))
                ->query(
                    OrderPackageItem::query()
                    ->with(['orderPackage.user'])
                    ->join('order_packages', 'order_package_items.order_package_id', '=', 'order_packages.id')
                    ->select([
                        'order_package_items.*', //
                        'order_packages.created_at as package_created_at'
                    ])
                    ->orderBy('package_created_at', 'desc')
                )
                ->groups([
                    Group::make('orderPackage.id')
                        ->label(__('messages.t_ap_order_id'))
                        ->getDescriptionFromRecordUsing(
                            function (OrderPackageItem $record): string {
                                return __('messages.t_ap_user_name') . ': ' . $record->orderPackage->user->name .
                                       ', ' . __('messages.t_ap_user_id') . ': ' . $record->orderPackage->user->id .
                                       ', ' . __('messages.t_ap_payment_method') . ': ' . ucfirst($record->orderPackage->payment_method) .
                                       ', ' . __('messages.t_ap_taxes') . ': ' . $record->orderPackage->taxes_value .
                                       ', ' . __('messages.t_ap_subtotal') . ': ' . $record->orderPackage->subtotal_value .
                                       ', ' . __('messages.t_ap_total') . ': ' . $record->orderPackage->total_value;
                            }
                        )
                ])
                ->defaultGroup('orderPackage.id')
                ->defaultPaginationPageOption(5)
                ->columns([
                    TextColumn::make('name')->label(__('messages.t_ap_package_name')),
                    TextColumn::make('activation_date')->label(__('messages.t_ap_activation_date'))->date(),
                    TextColumn::make('expiry_date')->label(__('messages.t_ap_expiry_date'))->date(),
                    TextColumn::make('price')->label(__('messages.t_ap_price')),
                    TextColumn::make('purchased')->label(__('messages.t_ap_purchased')),
                    TextColumn::make('available')->label(__('messages.t_ap_available')),
                    TextColumn::make('used')->label(__('messages.t_ap_used')),
                    TextColumn::make('orderPackage.created_at')->label(__('messages.t_ap_date'))->date(),
                    SelectColumn::make('orderPackage.status')->options([
                        'completed' => __('messages.t_ap_completed'),
                        'pending' => __('messages.t_ap_pending'),
                        'failed' => __('messages.t_ap_failed'),
                        'refunded' => __('messages.t_ap_refunded'),
                    ]),
                ]);
            } else {
                // Configuration for OrderUpgrade (previous example or similar)
                // Configuration for OrderUpgrade
                return $table
                    ->defaultSort('created_at', 'desc')
                    ->query(
                        OrderUpgrade::query()
                            ->with(['orderPromotions.adPromotion.promotion'])
                            ->latest()
                    )
                    ->defaultPaginationPageOption(5)
                    ->defaultSort('created_at', 'desc')
                    ->columns([
                        TextColumn::make('id')->label(__('messages.t_ap_order_id')),
                        TextColumn::make('ad_title')->label(__('messages.t_ap_ad_name')),
                        TextColumn::make('orderPromotions.adPromotion.promotion.name')->label(__('messages.t_ap_promotion_type')),
                        TextColumn::make('payment_method')->label(__('messages.t_ap_payment_method')),
                        TextColumn::make('taxes_value')->label(__('messages.t_ap_tax')),
                        TextColumn::make('subtotal_value')->label(__('messages.t_ap_subtotal')),
                        TextColumn::make('total_value')->label(__('messages.t_ap_total')),
                        TextColumn::make('created_at')->label(__('messages.t_ap_date'))->date(),
                        SelectColumn::make('status')->options([
                            'completed' => __('messages.t_ap_completed'),
                            'pending' => __('messages.t_ap_pending'),
                            'failed' => __('messages.t_ap_failed'),
                            'refunded' => __('messages.t_ap_refunded'),
                        ]),
                    ]);
        }
    }
}
