<?php

namespace App\Livewire\Reservation\Purchases;

use App\Models\Reservation\Order;
use App\Models\Ad;
use App\Models\CustomerReview;
use App\Models\Reservation\OrderStatusHistory;
use App\Models\Wallets\Wallet;
use App\Services\Wallet\TransactionServices;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Livewire\Attributes\Url;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Mokhosh\FilamentRating\Components\Rating;
use Illuminate\Support\Str;
use App\Traits\HasCommission;
use App\Traits\HandleOrderRefundTrait;
use App\Models\Reservation\RefundTransaction;
use Adfox\WalletSystem\Settings\WalletSystemSetting;


/**
 * MyAds Component.
 * Allows users to view and manage their ads with actions like preview, edit, and delete.
 */
class MyPurchases extends Component implements HasForms, HasTable
{
    use InteractsWithTable, InteractsWithForms, SEOToolsTrait, HasCommission, HandleOrderRefundTrait;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';
    public $activeTab = null;

    protected $listeners = ['tabChanged'];

    public $isDisplayTabs;
    public $tabs = [];
    /**
     * Mount lifecycle hook.
     */
    public function mount()
    {
        $this->setSeoData();

        if (is_ecommerce_active()) {
            $this->tabs[] = RESERVATION_TYPE_RETAIL;
            $this->activeTab = RESERVATION_TYPE_RETAIL;
        }

        if (isEnablePointSystem()) {
            $this->tabs[] = RESERVATION_TYPE_POINT_VAULT;
            $this->activeTab = $this->activeTab ?? RESERVATION_TYPE_POINT_VAULT;
        }

        $this->isDisplayTabs = false;
    }

    public function getQueryProperty()
    {
        return Order::query()->where('user_id', auth()->id())
            ->when($this->activeTab == RESERVATION_TYPE_RETAIL, fn($q) => $q->where('order_type', RESERVATION_TYPE_RETAIL))
            ->when($this->activeTab == RESERVATION_TYPE_POINT_VAULT, fn($q) => $q->where('order_type', RESERVATION_TYPE_POINT_VAULT));
        ;
    }

    /**
     * Defines the table structure for displaying ads.
     */

    public function table(Table $table): Table
    {

        return $table
            ->query($this->query)
            ->emptyStateIcon('/images/not-found.svg')
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query)
            ->columns([
                TextColumn::make('order_number')
                    ->label(__('messages.t_my_purchase_order_number'))
                    ->sortable(),
                TextColumn::make('ad')
                    ->default(function ($record) {
                        return $record->items->first()?->ad->title ?? '';
                    })
                    ->label(__('messages.t_ad_name'))
                    ->visible(fn($livewire) => isEnablePointSystem()),
                TextColumn::make('order_date')
                    ->label(__('messages.t_my_purchase_order_date'))
                    ->sortable(),
                TextColumn::make('points')
                    ->label(__('messages.t_my_purchase_points'))
                    ->visible(fn($livewire) => $this->activeTab == RESERVATION_TYPE_POINT_VAULT)
                    ->sortable(),
                TextColumn::make('exchange_rate')
                    ->label(__('messages.t_my_exchange_rate'))
                    ->hidden(fn($livewire) => $this->activeTab == RESERVATION_TYPE_POINT_VAULT)
                    ->sortable(),
                TextColumn::make('converted_amount')
                    ->label(__('messages.t_my_purchase_converted_amount'))
                    ->hidden(fn($livewire) => $this->activeTab == RESERVATION_TYPE_POINT_VAULT)
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(__('messages.t_my_purchase_total_amount'))
                    ->hidden(fn($livewire) => $this->activeTab == RESERVATION_TYPE_POINT_VAULT)
                    ->sortable(),
                    TextColumn::make('tax_amount')
                    ->label(__('messages.t_my_purchase_tax_amount'))
                    ->hidden(fn($livewire) => $this->activeTab == RESERVATION_TYPE_POINT_VAULT)
                    ->sortable(),
                // TextColumn::make('discount_amount')
                //     ->label(__('messages.t_my_purchase_discount_amount'))
                //     ->hidden(fn($livewire) => $this->activeTab == RESERVATION_TYPE_POINT_VAULT)
                //     ->sortable(),
                TextColumn::make('subtotal_amount')
                    ->label(__('messages.t_my_purchase_subtotal_amount'))
                    ->hidden(fn($livewire) => $this->activeTab == RESERVATION_TYPE_POINT_VAULT)
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->badge()
                    ->hidden(fn($livewire) => $this->activeTab == RESERVATION_TYPE_POINT_VAULT)
                    ->color('success')
                    ->formatStateUsing(fn(string $state): string => Str::title($state))
                    ->label(__('messages.t_my_purchase_payment_method'))
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->label(fn($livewire) => ($this->activeTab == RESERVATION_TYPE_POINT_VAULT) ? __('messages.t_my_purchase_point_earned') : __('messages.t_my_purchase_payment_status'))
                    ->default('-')
                    ->color(fn(string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'info',
                        default => 'warning'
                    })
                    ->formatStateUsing(fn(string $state): string => Str::title($state))
                    ->label(__('messages.t_my_purchase_payment_status'))
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy(function ($query) {
                                $query->select('action')
                                    ->from('order_status_histories')
                                    ->whereColumn('order_status_histories.order_id', 'orders.id')
                                    ->whereNotNull('action_date')
                                    ->orderBy('updated_at', 'desc')
                                    ->limit(1);
                            }, $direction);
                    })
                    ->badge()
                    ->default(function ($record) {
                        return $record->histories()->whereNotNull('action_date')->orderBy('updated_at', 'desc')->first()?->action;
                    })
                    ->color(fn(string $state): string => getOrderStatusColor($state))
                    ->formatStateUsing(fn(string $state): string => str_replace('_', ' ', Str::title($state)))
                    ->label(__('messages.t_my_tracking_status')),
                TextColumn::make('order_status')
                    ->label(__('messages.t_my_order_status'))
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'completed' => 'success',
                        'under_review' => 'info',
                        default => 'warning'
                    })
                    ->formatStateUsing(fn(string $state): string => Str::title(Str::replace('_', ' ', $state)))
                    ->hidden(fn($livewire) => $this->activeTab == RESERVATION_TYPE_POINT_VAULT),
                TextColumn::make('transaction_id')
                    ->label(__('messages.t_my_purchase_transaction_id'))
                    ->sortable()
                    ->hidden(fn($livewire) => $this->activeTab == RESERVATION_TYPE_POINT_VAULT),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->headerActions([
                Action::make('Refresh')
                    ->label(__('messages.t_refresh'))
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn() => $this->dispatch('refreshTable'))
            ])
            ->actions([
                Action::make('confirm_order_received')->action(function ($record, TransactionServices $transactionServices): void {
                    if ($record->order_type == RESERVATION_TYPE_POINT_VAULT) {
                        // Handle wallet and transaction for vendor
                        $transactionServices->createTransaction($record->vendor_id, $record->points, $record->order_number, $record->id, __('messages.t_order_received'));

                        // Handle wallet and transaction for user
                        $transactionServices->createTransaction($record->user_id, $record->points, $record->order_number, $record->id, __('messages.t_purchase_order'), false, canDeductPointsOnHold: true);
                    } else {
                        if ($record->payment_status == 'completed') {
                            $record->processingOrderCommission($record->subtotal_amount, $record->order_number, $record->vendor_id);
                        }
                    }

                    $record->histories()->where('action', 'order_received')->first()?->update([
                        'action_date' => now()
                    ]);
                })
                    ->button()
                    ->visible(function ($record) {
                        $histories = $record->histories()->get(); // Fetch all history records at once

                        if (
                            $histories->whereIn('action', ['order_cancelled', 'order_rejected'])
                                ->whereNotNull('action_date')->isNotEmpty()
                        ) {
                            return false;
                        }


                        if ($record->refundTransactions()->where('type', 'buyer')->exists()) {
                            return false;
                        }

                        return $histories->where('action', 'order_shipped')->whereNotNull('action_date')->isNotEmpty() &&
                            $histories->where('action', 'order_received')->whereNull('action_date')->isNotEmpty();
                    })
                    ->label(__('messages.t_order_received'))
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('messages.t_my_purchase_confirm_order_received_heading'))
                    ->modalDescription(__('messages.t_my_purchase_confirm_order_received_description')),
                CreateAction::make('order_not_received')
                    ->model(RefundTransaction::class)
                    ->form(function ($form) {
                        return $form
                            ->model(RefundTransaction::class)
                            ->schema(HandleOrderRefundTrait::orderNotReceivedForm());
                    })
                    ->mutateFormDataUsing(function (array $data, $record): array {
                        $data['user_id'] = auth()->id();
                        $data['order_id'] = $record->id;
                        $data['type'] = 'buyer';

                        return $data;
                    })
                    ->after(function ($record) {
                        $order = $record->order;
                        if ($order) {
                            $order->histories()->where('action', 'order_not_received')->first()?->update([
                                'action_date' => now(),
                            ]);
                        }
                    })
                    ->button()
                    ->visible(function ($record) {
                        if (isEnablePointSystem()) {
                            return false;
                        }
                        $histories = $record->histories()->get();

                        if ($histories->where('action', 'order_rejected')->whereNotNull('action_date')->isNotEmpty()) {
                            return false;
                        }
                        $shippingDate = $record->histories()->where('action', 'order_shipped')->first()?->action_date;
                        $DeliveryConfirmationTime = app(WalletSystemSetting::class)->delivery_confirmation_time;
                        return $record->order_status != 'completed' && $record->payment_status == 'completed' && $shippingDate && now()->diffInDays($shippingDate) >= (int) $DeliveryConfirmationTime;

                    })
                    ->hidden(function ($record) {
                        return $record->refundTransactions()->where('type', 'buyer')->exists();
                    })
                    ->label(__('messages.t_order_not_received'))
                    ->color('danger')
                    ->modalHeading(__('messages.t_report_order_not_received_heading'))
                    ->modalDescription(function ($record) {
                        $DeliveryConfirmationTime1 = app(WalletSystemSetting::class)->delivery_confirmation_time;
                        return __('messages.t_report_order_not_received_description', ['days' => $DeliveryConfirmationTime1]);
                    })
                    ->modalSubmitActionLabel(__('messages.t_submit'))
                    ->modalCancelActionLabel(__('messages.t_cancel'))
                    ->disableCreateAnother()
                    ->successNotificationTitle(__('messages.t_refund_request_send')),
                Action::make('view_status')
                    ->label(__('messages.t_view_status_refund'))
                    ->modalSubmitAction(false)
                    ->color('info')
                    ->visible(function ($record) {
                        return $record->refundTransactions()->where('type', 'buyer')->exists();
                    })
                    ->modalHeading(fn() => __('messages.t_refund_status'))
                    ->infolist(fn($record) => self::generateRefundStatusFields($record)),
                Action::make('write_a_here')
                    ->icon('heroicon-o-pencil-square')
                    ->color('info')
                    ->label(__('messages.t_my_purchase_write_a_review'))
                    ->form([
                        Select::make('product')
                            ->required()
                            ->options(function ($record) {
                                return Ad::whereIn('id', $record->items->pluck('ad_id'))->pluck('title', 'id')->toArray();
                            })->disableOptionWhen(fn(string $value, $record): bool => in_array($value, CustomerReview::where('order_id', $record->id)->pluck('reviewable_id')->toArray())),
                        Rating::make('rate_your_self')
                            ->label(__('messages.t_my_purchase_rate_the_product'))
                            ->required()
                            ->size('lg')
                            ->helperText(__('messages.t_my_purchase_rate_the_product_helper')),
                        Textarea::make('write_your_feedback')->maxLength(300)
                            ->rows(10)
                            ->cols(20)
                            ->helperText(__('messages.t_my_purchase_write_your_feedback'))
                    ])->action(function ($data, $record) {
                        $record->customerReviews()->create([
                            'reviewable_id' => $data['product'],
                            'reviewable_type' => Ad::class,
                            'rating' => $data['rate_your_self'],
                            'feedback' => $data['write_your_feedback'],
                            'user_id' => auth()->user()->id
                        ]);

                        Notification::make()
                            ->title(__('messages.t_my_purchase_review_send_successfully'))
                            ->success()
                            ->send();
                    })
                    ->visible(function ($record) {
                        if (isEnablePointSystem()) {
                            return false;
                        }
                        return $record->histories()->whereNotNull('action_date')->where('action', 'order_received')->exists();
                    }),
                Action::make('view')
                    ->hidden(fn() => isEnablePointSystem())
                    ->icon('heroicon-o-eye')
                    ->label(__('messages.t_my_purchase_view_my_purchases'))
                    ->action(function ($record) {
                        return redirect()->route('reservation.view-purchases', $record->id);
                    })
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }

    public static function generateRefundStatusFields($record)
    {
        return [
            Section::make(__('messages.t_placed_order_id') . $record->order_number)
                ->schema([
                    Grid::make(2) // 2 columns grid layout
                        ->schema([
                            SpatieMediaLibraryImageEntry::make('buyerRefundTransactions.proof_attachment')
                                ->label(__('messages.t_ap_payment_proof'))
                                ->collection('order_refund_proof')
                                ->visibility('public'),

                            SpatieMediaLibraryImageEntry::make('buyerRefundTransactions.summary_attachment')
                                ->label(__('messages.t_ap_refund_order_summary_proof_images'))
                                ->collection('order_summary_proof')
                                ->visibility('public'),
                        ]),

                    Grid::make(2) // Another grid for text fields
                        ->schema([
                            TextEntry::make('buyerRefundTransactions.description')
                                ->label(__('messages.t_description')),

                            TextEntry::make('buyerRefundTransactions.status')
                                ->label(__('messages.t_refund_status'))
                                ->badge()
                                ->color(fn($state) => match ($state) {
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    default => 'gray',
                                }),
                        ]),
                ]),
        ];
    }




    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;

        $this->getQueryProperty();
    }

    /**
     * Set SEO data
     */
    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);

        $separator = $generalSettings->separator ?? '-';
        $siteName = $generalSettings->site_name ?? app_name();

        $title = __('messages.t_seo_my_purchases_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    /**
     * Renders the MyAds view.
     */
    public function render()
    {
        return view('livewire.reservation.purchases.my-purchases');
    }
}
