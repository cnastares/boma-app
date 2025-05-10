<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;

use App\Settings\GeneralSettings;
use App\Settings\PaymentSettings;
use App\Settings\PaystackSettings;
use App\Settings\RazorpaySettings;
use App\Settings\SEOSettings;
use App\Settings\SubscriptionSettings;
use Livewire\Component;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Livewire\Attributes\Url;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use App\Models\OrderPackage;
use App\Models\OrderUpgrade;
use App\Models\SettingsProperty;
use App\Settings\PackageSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\Action;
use App\Settings\StripeSettings;
use App\Settings\PaypalSettings;
use App\Models\Promotion;
use App\Settings\FlutterwaveSettings;
use App\Settings\OfflinePaymentSettings;
use App\Settings\PaymongoSettings;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Number;

class MyOrders extends Page implements HasForms, HasTable
{
    use SEOToolsTrait;
    use InteractsWithTable;
    use InteractsWithForms;
    // protected static ?string $navigationIcon = 'bag-dollar';
    // protected static ?string $navigationGroup = 'Subscriptions & Orders';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.app.pages.my-orders';
    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';

    #[Url(as: 'active-table', keep: true)]
    public $activeTable = 'upgrade';

    public static function getNavigationGroup(): ?string
    {
        return __('messages.t_engagements_navigation');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_my_orders');
    }
    public  function getTitle(): string
    {
        return __('messages.t_my_orders');
    }
    /**
     * Mount the component
     */
    public function mount()
    {
        if (($this->subscriptionSettings->status &&  !$this->subscriptionSettings->combine_subscriptions_and_orders) || (!$this->subscriptionSettings->status)) {
            $this->activeTable = 'upgrade';
        }
        $this->setSeoData();
    }

    /**
     * Defines the table structure for displaying package items or upgrade items based on a condition.
     */
    public function table(Table $table): Table
    {
        // Check if the 'packages' plugin is installed
        if (app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status) {
            // Query for OrderPackage items based on the current user
            $query = OrderPackage::query()->where('user_id', auth()->id());
            $columns = [
                TextColumn::make('id')->label(__('messages.t_transaction_id')),
                TextColumn::make('payment_method')->label(__('messages.t_payment_method'))
                    ->formatStateUsing(fn(string $state) => getPaymentLabel($state)),
                TextColumn::make('total_value')->formatStateUsing(fn($state, PaymentSettings $paymentSettings) => Number::format(floatval($state), locale: $paymentSettings->currency_locale))->label(__('messages.t_total_value'))->prefix(config('app.currency_symbol')),
                TextColumn::make('subtotal_value')->formatStateUsing(fn($state, PaymentSettings $paymentSettings) => Number::format(floatval($state), locale: $paymentSettings->currency_locale))->label(__('messages.t_subtotal_value'))->prefix(config('app.currency_symbol')),
                TextColumn::make('taxes_value')->formatStateUsing(fn($state, PaymentSettings $paymentSettings) => Number::format(floatval($state), locale: $paymentSettings->currency_locale))->label(__('messages.t_taxes_value'))->prefix(config('app.currency_symbol')),
                TextColumn::make('status')->label(__('messages.t_status'))
                    ->formatStateUsing(fn(string $state): string => __('messages.t_' . $state)),
                TextColumn::make('created_at')->label(__('messages.t_created_at'))->date('d/m/Y'),
            ];
        } else {
            // Query for OrderUpgrade items based on the current user
            $query = OrderUpgrade::query()->whereHas('user', function ($query) {
                $query->where('id', auth()->id());
            });
            $columns = [
                TextColumn::make('id')->label(__('messages.t_transaction_id')),
                TextColumn::make('payment_method')->label(__('messages.t_payment_method'))
                    ->formatStateUsing(fn(string $state) => getPaymentLabel($state)),
                TextColumn::make('total_value')->formatStateUsing(fn($state, PaymentSettings $paymentSettings) => Number::format(floatval($state), locale: $paymentSettings->currency_locale))->label(__('messages.t_total_value'))->prefix(config('app.currency_symbol')),
                TextColumn::make('subtotal_value')->formatStateUsing(fn($state, PaymentSettings $paymentSettings) => Number::format(floatval($state), locale: $paymentSettings->currency_locale))->label(__('messages.t_subtotal_value'))->prefix(config('app.currency_symbol')),
                TextColumn::make('taxes_value')->formatStateUsing(fn($state, PaymentSettings $paymentSettings) => Number::format(floatval($state), locale: $paymentSettings->currency_locale))->label(__('messages.t_taxes_value'))->prefix(config('app.currency_symbol')),
                TextColumn::make('status')->label(__('messages.t_status'))
                    ->formatStateUsing(fn(string $state): string => __('messages.t_' . $state)),
                TextColumn::make('created_at')->label(__('messages.t_created_at'))->date('d/m/Y'),
            ];
        }

        return $table
            ->defaultSort('created_at', 'desc')
            // ->emptyStateIcon('/images/not-found.svg')
            ->emptyState(view('tables.empty-state', ['message' => __('messages.t_no_order_packages')]))
            ->query($query)
            ->columns($columns)
            ->filters([
                // Add any filters if required
            ])
            ->actions([
                Action::make('download_invoice')
                    ->label(__('messages.t_download_invoice'))
                    ->icon('heroicon-o-document-text')
                    ->hidden(fn($record) => $record->getTable() != 'order_upgrades')
                    ->action(function ($record, $livewire) {
                        try {

                            $order = $record;
                            $pdfData = collect();
                            $pdfData->put('name', __('messages.t_pdf_invoice_title'));
                            $pdfData->put('status', $order->status);
                            $pdfData->put('invoice_id', $order->invoice_id);
                            $pdfData->put('invoice_date', $order->created_at);
                            $pdfData->put('subtotal', $livewire->formatCurrency($livewire->getExchangeCurrencySymbol($record->payment_method), ($order?->subtotal_value)));
                            $pdfData->put('total', $livewire->formatCurrency($livewire->getExchangeCurrencySymbol($record->payment_method), ($order?->total_value)));
                            $pdfData->put('tax', $livewire->formatCurrency($livewire->getExchangeCurrencySymbol($record->payment_method), ($order?->taxes_value)));

                            //Buyer Details
                            $buyer = [
                                'name' => auth()->user()->name,
                                'email' => auth()->user()->email,
                            ];
                            $pdfData->put('buyer', $buyer);

                            //Logo Details
                            $logoUrl = $livewire->getLogo();
                            $path = $logoUrl;
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $logoData = file_get_contents($path);
                            $logo = 'data:image/' . $type . ';base64,' . base64_encode($logoData);
                            $pdfData->put('logo', $logo);

                            //Order Item Details
                            $itemDetails = [];
                            $items = $record->orderPromotions;
                            if (count($items)) {
                                foreach ($items as $item) {
                                    $adPromotion = $item->adPromotion;
                                    if ($adPromotion) {
                                        $promotion = $adPromotion->promotion;
                                        if ($promotion) {
                                            $price = $livewire->formatCurrency($livewire->getExchangeCurrencySymbol($record->payment_method), (int) $promotion?->price);
                                            $total = $price;
                                            $itemDetails[] = [
                                                'name' => $promotion?->name,
                                                'quantity' => 1,
                                                'price' => $price,
                                                'total' => $total,
                                            ];
                                        }
                                    }
                                }
                            }

                            $pdfData->put('items', $itemDetails);
                            $data = [
                                'invoice' => $pdfData
                            ];

                            $pdf = Pdf::loadView('pdf-templates.invoice', $data);
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->stream();
                            }, 'invoice_' . $record->id . '.pdf');
                        } catch (\Throwable $throwable) {
                            // Error
                            Notification::make()
                                ->title(__('messages.t_toast_something_went_wrong'))
                                ->body($throwable->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
            ]);
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

        $title = __('messages.t_my_orders') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    public function formatCurrency($currency, $amount)
    {
        $paymentSettings = app(PaymentSettings::class);
        $amount = floatval($amount);
        $locale = $paymentSettings->currency_locale;
        return Number::currency($amount, in: $currency, locale: $locale);
    }
    public function getExchangeCurrencySymbol($paymentMethod)
    {
        return match ($paymentMethod) {
            'stripe' => app(StripeSettings::class)->currency,
            'paypal' => app(PaypalSettings::class)->currency,
            'flutterwave' => app(FlutterwaveSettings::class)->currency,
            'offline' => app(OfflinePaymentSettings::class)->currency,
            'paymongo' => app(PaymongoSettings::class)->currency,
            'paystack' => app(PaystackSettings::class)->currency,
            'razorpay' => app(RazorpaySettings::class)->currency,
            default => '$'
        };
    }
    public function getSubscriptionSettingsProperty()
    {
        return app(SubscriptionSettings::class);
    }
    public function getLogo()
    {
        $settingsProperty = SettingsProperty::getInstance('general.logo_path');

        if ($settingsProperty) {
            $media = $settingsProperty->getFirstMedia('logo');

            if ($media) {
                // Otherwise, return the URL
                return $media->getPath();
            }
        }
        return public_path('images/logo.svg');
    }
}
