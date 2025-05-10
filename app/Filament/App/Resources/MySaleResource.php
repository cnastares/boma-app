<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MySaleResource\Pages;
use App\Models\Page;
use App\Models\Reservation\Order;
use App\Models\Reservation\OrderStatusHistory;
use App\Models\Wallets\Wallet;
use App\Services\Wallet\TransactionServices;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HandleOrderRefundTrait;
use App\Models\Reservation\RefundTransaction;
use Adfox\WalletSystem\Settings\WalletSystemSetting;
use Carbon\Carbon;



class MySaleResource extends Resource
{
    protected static ?string $model = Order::class;

    public static function getNavigationGroup(): ?string
    {
        return is_ecommerce_active() ? __('messages.t_ecommerce_navigation') : (isEnablePointSystem() ? __('messages.t_sales_booking_navigation') : null);
    }

    public static function isDiscovered(): bool
    {
        return is_ecommerce_active() || isEnablePointSystem();
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_my_sales');
    }

    public function getTitle(): string
    {
        return __('messages.t_my_sales');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->model(RefundTransaction::class)
            ->schema(HandleOrderRefundTrait::orderNotReceivedForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->where('vendor_id', auth()->id()))
            ->emptyStateIcon('/images/not-found.svg')
            ->columns(self::getColumns())
            ->filters([
                //
            ])
            ->actions([
                Action::make('order_accept')
                    ->action(function ($record) {
                        if ($record->order_type == RESERVATION_TYPE_POINT_VAULT) {
                            // Update payment status and order history
                            $record->payment_status = 'completed';
                            $record->save();

                            //When order is accepted, update the ad status to 'sold'. This is only applicable when point system is enabled.
                            $orderItem = $record->items->first();
                            if ($orderItem) {
                                $ad = $orderItem->ad;
                                if ($ad) {
                                    $ad->update(['status' => 'sold']);
                                }
                            }
                        }
                        $record->histories()->where('action', 'order_accepted')->first()?->update(['action_date' => now()]);
                    })
                    ->button()
                    ->color('success')
                    ->visible(function ($record) {
                        return $record->histories()->whereNull('action_date')->where('action', 'order_accepted')->exists() &&
                            $record->histories()->whereNull('action_date')->where('action', 'order_rejected')->exists();
                    })
                    ->label(__('messages.t_my_sale_order_accept_button'))
                    ->requiresConfirmation()
                    ->modalHeading(__('messages.t_my_sale_confirm_order_acceptance_heading'))
                    ->modalDescription(__('messages.t_my_sale_confirm_order_acceptance_description'))
                    ->modalContent(function ($record) {
                        $page = Page::find(getPointSystemSetting('policy_page'));

                        return view('filament.modals.order-accept', ['record' => $record, 'slug' => $page?->slug]);
                    })->modalButton(__('messages.t_my_sale_order_accept_button')),

                // Order reject action
                Action::make('order_reject')
                    ->action(function ($record, TransactionServices $transactionServices) {
                        // Update rejection history and payment status
                        $record->histories()->where('action', 'order_rejected')->first()?->update(['action_date' => now()]);
                        if ($record->order_type == RESERVATION_TYPE_POINT_VAULT) {
                            $record->payment_status = 'refund';
                            $record->save();

                            // Handle wallet and transaction for user on refund
                            $transactionServices->refundTransaction($record->user_id, $record->points, $record->order_number, $record->id, __('messages.t_order_rejected_refunded'), true);
                        } else {
                            RefundTransaction::create([
                                'order_id' => $record->id,
                                'user_id' => $record->user_id,
                                'type' => 'buyer', // Or use ENUM if applicable
                                'description' => __('messages.t_ap_order_rejected_refunded_to_wallet'),
                            ]);
                        }
                    })
                    ->requiresConfirmation()
                    ->button()
                    ->color('danger')
                    ->modalHeading(__('messages.t_my_sale_confirm_order_reject_heading'))
                    ->modalDescription(__('messages.t_my_sale_confirm_order_reject_description'))
                    ->label(__('messages.t_my_sale_order_reject_button'))
                    ->visible(function ($record) {
                        return $record->histories()->whereNull('action_date')->where('action', 'order_accepted')->exists() &&
                            $record->histories()->whereNull('action_date')->where('action', 'order_rejected')->exists();
                    }),

                self::makeRefundRequest(),
                Action::make('view_status')
                ->label(__('messages.t_view_status_refund'))
                ->modalSubmitAction(false)
                ->color('info')
                ->visible(function ($record) {
                    return $record->refundTransactions()->where('type', 'seller')->exists();
                })
                ->modalHeading(fn() => __('messages.t_refund_status'))
                ->infolist(fn($record) => self::generateRefundStatusFields($record)),
                Action::make('cancel')
                    ->label(__('messages.t_cancel_order'))
                    ->button()
                    ->hidden(function ($record) {
                        if (getECommerceSystemSetting('enable_e_commerce')) {
                            return true;
                        }
                        $isHidden = $record->histories()
                            ->where(function ($query) {
                                $query->where(function ($subQuery) {
                                    // Case 1: Hide if action is rejected/received and action_date exists
                                    $subQuery->whereNotNull('action_date')
                                        ->whereIn('action', ['order_rejected', 'order_received', ['order_cancelled']]);
                                })
                                    ->orWhere(function ($subQuery) {
                                        // Case 2: Hide if action is shipped and action_date is null
                                        $subQuery->whereNull('action_date')
                                            ->where('action', 'order_shipped');
                                    });
                            })
                            ->exists();
                        return $isHidden;
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('messages.t_cancel_order_heading'))
                    ->modalDescription(__('messages.t_cancel_order_description'))
                    ->action(function ($record, TransactionServices $transactionServices) {
                        if ($record->order_type == RESERVATION_TYPE_POINT_VAULT) {
                            $record->payment_status = 'refund';
                            $record->save();

                            // Handle wallet and transaction for user on refund
                            $transactionServices->refundTransaction($record->user_id, $record->points, $record->order_number, $record->id, __('messages.t_order_rejected_refunded'), true);

                            //When order is accepted, update the ad status to 'active'. This is only applicable when point system is enabled.
                            $orderItem = $record->items->first();
                            if ($orderItem) {
                                $ad = $orderItem->ad;
                                if ($ad) {
                                    $ad->update(['status' => 'active']);
                                }
                            }
                        }
                        $record->histories()->create([
                            'user_id' => $record->user_id,
                            'vendor_id' => $record->vendor_id,
                            'action' => 'order_cancelled',
                            'command' => __('messages.t_by_seller'),
                            'action_date' => now(),
                        ]);
                    }),

                // View action
                Action::make('view')
                    ->hidden(fn(): bool => isEnablePointSystem())
                    ->icon('heroicon-o-eye')
                    ->label(__('messages.t_view_my_purchases'))
                    ->action(fn($record) => redirect()->route('reservation.view-purchases', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function generateRefundStatusFields($record)
    {
        return [
            Section::make(__('messages.t_placed_order_id').$record->order_number)
                ->schema([
                    Grid::make(2) // 2 columns grid layout
                        ->schema([
                            SpatieMediaLibraryImageEntry::make('sellerRefundTransactions.proof_attachment')
                                ->label(__('messages.t_ap_delivery_proof'))
                                ->collection('order_refund_proof')
                                ->visibility('public'),

                            SpatieMediaLibraryImageEntry::make('sellerRefundTransactions.summary_attachment')
                                ->label(__('messages.t_ap_refund_order_summary_proof_images'))
                                ->collection('order_summary_proof')
                                ->visibility('public'),
                        ]),

                    Grid::make(2) // Another grid for text fields
                        ->schema([
                            TextEntry::make('sellerRefundTransactions.description')
                                ->label(__('messages.t_description')),

                            TextEntry::make('sellerRefundTransactions.status')
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


    public static function getColumns(): array
    {
        return [
            TextColumn::make('order_number')
                ->label(__('messages.t_my_sale_order_number'))
                ->sortable(),

            TextColumn::make('ad')
                ->default(function ($record) {
                    return $record->items->first()?->ad->title ?? '';
                })
                ->label(__('messages.t_ad_name'))
                ->visible(fn() => isEnablePointSystem()),

            TextColumn::make('order_date')
                ->label(__('messages.t_my_sale_order_date'))
                ->sortable(),

            TextColumn::make('points')
                ->label(__('messages.t_my_sale_points'))
                ->visible(fn($livewire) => isEnablePointSystem())
                ->sortable(),

            TextColumn::make('total_amount')
                ->label(__('messages.t_my_sale_total_amount'))
                ->hidden(fn($livewire) => isEnablePointSystem())
                ->sortable(),

            TextColumn::make('discount_amount')
                ->label(__('messages.t_my_sale_discount_amount'))
                ->hidden(fn($livewire) => isEnablePointSystem())
                ->sortable(),

            TextColumn::make('subtotal_amount')
                ->label(__('messages.t_my_sale_subtotal_amount'))
                ->hidden(fn($livewire) => isEnablePointSystem())
                ->sortable(),

            TextColumn::make('payment_method')
                ->badge()
                ->hidden(fn($livewire) => isEnablePointSystem())
                ->color(fn(string $state): string => self::getPaymentMethodColor($state))
                ->label(__('messages.t_my_sale_payment_method'))
                ->sortable(),

            TextColumn::make('payment_status')
                ->badge()
                ->color(fn(string $state): string => $state === 'completed' ? 'success' : ($state === 'refund' ? 'danger' : 'info'))
                ->formatStateUsing(fn(string $state): string => Str::title($state))
                ->label(fn($livewire) => (isEnablePointSystem())
                    ? __('messages.t_my_sale_point_earned')
                    : __('messages.t_my_sale_payment_status'))
                ->sortable(),

            TextColumn::make('transaction_id')
                ->label(__('messages.t_my_sale_transaction_id'))
                ->hidden(fn($livewire) => isEnablePointSystem())
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
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->default(fn($record) => self::getLatestOrderStatus($record))
                ->color(fn(string $state): string => self::getOrderStatusColor($state))
                ->formatStateUsing(fn(string $state): string => str_replace('_', ' ', Str::title($state)))
                ->label(__('messages.t_my_sale_status')),
            TextColumn::make('order_status')
                ->label(__('messages.t_my_order_status'))
                ->sortable()
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'completed' => 'success',
                    'under_review' => 'info',
                    default => 'warning'
                })
                ->hidden(isEnablePointSystem())
                ->formatStateUsing(fn(string $state): string => Str::title(Str::replace('_', ' ', $state))),
            SelectColumn::make('change_order_status')
                ->disabled(fn($record) => $record ? !$record->histories()->whereNotNull('action_date')->where('action', 'order_accepted')->exists() : false)
                ->options(fn($record) => self::getOrderStatusOptionsForSelect($record))
                ->updateStateUsing(fn($record, $state) => self::updateAdStatus($record, $state))
                ->disableOptionWhen(fn(string $value, $record): bool => self::isStatusOptionDisabled($value, $record))
                ->label(__('messages.t_my_sale_change_status_action'))

        ];
    }

    public static function getPaymentMethodColor(string $state): string
    {
        return match ($state) {
            'stripe', 'paypal', 'flutterwave', 'paymongo', 'paystack', 'razorpay' => 'success',
            'offline' => 'info',
            default => 'secondary',
        };
    }

    public static function getLatestOrderStatus($record): ?string
    {
        return $record->histories()
            ->whereNotNull('action_date')
            ->orderBy('updated_at', 'desc')
            ->first()?->action;
    }

    public static function getOrderStatusColor(string $state): string
    {
        return match ($state) {
            'order_requested', 'order_accepted', 'order_received' => 'info',
            'order_processed',
            'order_shipped' => 'warning',
            'order_cancelled', 'order_rejected', 'order_not_received' => 'danger',
            default => 'secondary',
        };
    }

    public static function getOrderStatusOptionsForSelect($record): array
    {
        if (isEnablePointSystem()) {
            $orderStatuses = ['order_processed', 'order_received', 'order_rejected', 'order_not_received'];
        } else {
            $orderStatuses = ['order_received', 'order_rejected', 'order_not_received'];
        }
        $status = OrderStatusHistory::where('order_id', $record->id)
            ->whereNotIn('action', $orderStatuses)
            ->pluck('action')
            ->toArray();

        $action = OrderStatusHistory::where('order_id', $record->id)
            ->whereNull('action_date')->first()?->action;

        $array = [
            'order_requested' => __('messages.t_order_request'),
            'order_accepted' => __('messages.t_order_accepted'),
            'order_shipped' => __('messages.t_order_shipped'),
            'order_received' => __('messages.t_order_received'),
            'order_cancelled' => __('messages.t_order_cancelled'),
            'order_not_received' => __('messages.t_order_not_received'),
        ];

        $returnArray = [];



        foreach ($status as $value) {
            $returnArray[$value] = $array[$value];

            if ($action == $value)
                break;
        }

        return $returnArray;
    }

    public static function isStatusOptionDisabled(string $value, $record): bool
    {
        $completedActions = OrderStatusHistory::where('order_id', $record->id)
            ->whereNotNull('action_date')
            ->pluck('action')
            ->toArray();

        return in_array($value, $completedActions);
    }

    public static function updateAdStatus($record, string $state)
    {
        $record->histories()->where('action', $state)->first()?->update([
            'action_date' => now()
        ]);

        Notification::make()
            ->title(__('messages.t_updated_successfully'))
            ->success()
            ->send();
    }

    public static function handleWalletTransaction($userId, $points, $orderNumber, $orderId, $transactionType, $isAdded = true)
    {
        // Fetch or create the user's wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $userId],  // Query condition
            ['points' => 0] // Default balance for new wallet
        );

        // Increment wallet balance
        if ($isAdded) {
            $wallet->increment('points', $points);
        }

        // Create a transaction record for the wallet
        $wallet->transactions()->create([
            'user_id' => $userId,
            'points' => $points,
            'transaction_reference' => $orderNumber,
            'transaction_type' => $transactionType,
            'is_added' => $isAdded,
            'status' => 'completed',
            'payable_type' => Order::class,  // Polymorphic model type
            'payable_id' => $orderId,        // Polymorphic model id
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function makeRefundRequest()
    {
        return
            CreateAction::make('report_mysale')
                ->model(RefundTransaction::class)
                ->mutateFormDataUsing(function (array $data, $record): array {
                    $data['user_id'] = auth()->id();
                    $data['order_id'] = $record->id;
                    $data['type'] = 'seller';

                    return $data;
                })
                ->after(function ($record) {
                    $order = $record->order;
                    if ($order) {
                        $order->where('order_number', $order->order_number)->first()?->update([
                            'order_status' => 'under_review',
                        ]);
                    }
                })
                ->button()
                ->visible(function ($record) {
                    if (isEnablePointSystem()) {
                        return false;
                    }
                    $DeliveryConfirmationTime = app(WalletSystemSetting::class)->delivery_confirmation_time;
                    $order = Order::where('order_number', $record->order_number)->where('payment_status', 'completed')->where('order_status', '!=', 'completed')
                        ->whereHas('histories', function ($query) use ($DeliveryConfirmationTime) {
                            $query->where('action', 'order_shipped')
                                ->whereNotNull('action_date')->where('action', '!=', 'order_rejected')
                                ->where('action_date', '<=', Carbon::now()->subDays($DeliveryConfirmationTime));
                        })->first();

                    if ($order) {
                        return true;
                    } else {
                        return false;
                    }

                })
                ->hidden(function ($record) {
                    return $record->refundTransactions()->where('type', 'seller')->exists();
                })
                ->label(__('messages.t_report_mysale'))
                ->color('danger')
                ->modalHeading(__('messages.t_report_order_not_received_heading'))
                ->modalSubmitActionLabel(__('messages.t_submit'))
                ->modalCancelActionLabel(__('messages.t_cancel'))
                ->disableCreateAnother()
                ->successNotificationTitle(__('messages.t_refund_request_send'));
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMySales::route('/'),
            // 'create' => Pages\CreateMySale::route('/create'),
            // 'edit' => Pages\EditMySale::route('/{record}/edit'),
        ];
    }
}
