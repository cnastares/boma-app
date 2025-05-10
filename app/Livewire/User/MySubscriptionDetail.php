<?php

namespace App\Livewire\User;

use App\Models\Invoice;
use App\Models\SettingsProperty;
use App\Models\Subscription;
use App\Settings\GeneralSettings;
use App\Settings\PaymentSettings;
use App\Settings\PaypalSettings;
use App\Settings\SEOSettings;
use App\Settings\StripeSettings;
use App\Settings\SubscriptionSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Livewire\Component;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Livewire\Attributes\Url;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class MySubscriptionDetail extends Component implements HasForms, HasTable
{
    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';
    use SEOToolsTrait;
    use InteractsWithTable;
    use InteractsWithForms;


    /**
     * Mount the component
     */
    public function mount() {}

    /**
     * Defines the table structure for displaying package items.
     */
    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->query(
                Subscription::query()
                    ->whereHas('subscriber', function ($query) {
                        $query->where('id', auth()->id());
                    })
            )
            ->columns([
                TextColumn::make('plan.name')->label(__('messages.t_plan')),
                TextColumn::make('status')->label(__('messages.t_status'))
                    ->formatStateUsing(fn(string $state): string => __("messages.t_" . strtolower($state))),
                TextColumn::make('paid_amount')
                    ->default(function (PaymentSettings $paymentSettings, $record) {
                        $latestPaidInvoice = $record->invoices()->where('status', 'paid')->latest()->first();
                        $latestAmountPaid = $latestPaidInvoice ? $latestPaidInvoice->amount_paid : 0;
                        return \Number::format(floatval($latestAmountPaid), locale: $paymentSettings->currency_locale);
                    })->label(__('messages.t_paid_amount'))->prefix(config('app.currency_symbol')),
                TextColumn::make('price')
                    ->label(__('messages.t_next_due_amount'))->formatStateUsing(fn($state, PaymentSettings $paymentSettings, $record) => \Number::format(floatval($state), locale: $paymentSettings->currency_locale))->prefix(config('app.currency_symbol')),
                TextColumn::make('starts_at')->label(__('messages.t_starts_at'))
                    ->date('d/m/Y'),
                TextColumn::make('ends_at')->label(__('messages.t_ends_at'))
                    ->date('d/m/Y'),
                TextColumn::make('cancels_at')->label(__('messages.t_cancels_at'))
                    ->date('d/m/Y'),

            ])
            ->actions([
                Action::make('cancel')
                    ->label(__('messages.t_cancel'))
                    ->visible(fn($record) =>$record->status=="active" && is_null($record->cancels_at) && $record->ends_at->isFuture())
                    ->requiresConfirmation()
                    ->icon('heroicon-o-x-circle')
                    ->modalDescription(__('messages.t_subscription_cancel_description'))
                    ->modalHeading(__('messages.t_subscription_cancel_heading'))
                    ->color('danger')
                    ->action(function ($record) {
                        if ($record && $record->subscription_reference) {
                            try {
                                $this->updateSubscriptionCancelStatus($record, true);
                                Notification::make()
                                    ->title(__('messages.t_cancel_action_notification_title'))
                                    ->success()
                                    ->body(__('messages.t_cancel_action_notification_body'))
                                    ->send();
                            } catch (\Throwable $throwable) {
                                Notification::make()
                                    ->title(__('messages.t_error_payment_failed'))
                                    ->danger()
                                    ->body($throwable->getMessage())
                                    ->send();
                            }
                        }
                    }),
                Action::make('dont_cancel')
                    ->label(__("messages.t_dont_cancel"))
                    ->visible(fn($record) =>$record->status=="active" &&  isset($record->cancels_at) && $record->cancels_at->isFuture())
                    ->requiresConfirmation()
                    ->icon('heroicon-o-x-circle')
                    ->modalHeading(__('messages.t_subscription_dont_cancel_heading'))
                    ->modalDescription(__('messages.t_subscription_dont_cancel_description'))
                    ->color('danger')
                    ->action(function ($record) {
                        if ($record && $record->subscription_reference) {
                            try {
                                $this->updateSubscriptionCancelStatus($record, false);
                                Notification::make()
                                    ->title(__('messages.t_dont_cancel_action_notification_title'))
                                    ->success()
                                    ->body(__('messages.t_dont_cancel_action_notification_body'))
                                    ->send();
                            } catch (\Throwable $throwable) {
                                Notification::make()
                                    ->title(__('messages.t_error_payment_failed'))
                                    ->danger()
                                    ->body($throwable->getMessage())
                                    ->send();
                            }
                        }
                    }),
                Action::make('download_invoice')
                    ->label(__('messages.t_download_invoice'))
                    ->icon('heroicon-o-document-text')
                    ->action(function ($record) {
                        try {
                            $invoice = Invoice::where('subscription_id', $record->id)->latest()->first();
                            if (!$invoice) {
                                Notification::make()
                                    ->title('Invoice not available')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $invoiceData = null;

                            if ($record->payment_method == 'stripe') {
                                $stripe = new \Stripe\StripeClient(app(StripeSettings::class)?->secret_key, null);
                                $invoiceData = $stripe->invoices->retrieve(
                                    $invoice->invoice_id,
                                    []
                                );
                            } else if ($record->payment_method == 'paypal') {
                                $invoiceData = (object) [
                                    'total' => $invoice->amount_paid * 100,
                                    'subtotal' => $invoice->amount_paid * 100,
                                ];
                            }
                            $discountAmount=isset($invoiceData?->total_discount_amounts[0])?$this->formatCurrency($invoice->currency, ($invoiceData?->total_discount_amounts[0]->amount / 100)): null;
                            $pdfData = collect();
                            $pdfData->put('name', __('messages.t_pdf_invoice_title'));
                            $pdfData->put('status', $invoice->status);
                            $pdfData->put('invoice_id', $invoice->invoice_id);
                            $pdfData->put('invoice_date', $invoice->invoice_date);
                            $pdfData->put('due_date', $invoice->due_date);
                            $pdfData->put('total', $this->formatCurrency($invoice->currency, ($invoiceData?->total / 100)));
                            $pdfData->put('discount',$discountAmount );
                            $pdfData->put('subtotal', $this->formatCurrency($invoice->currency, ($invoiceData?->subtotal / 100)));
                            // $pdfData->put('tax', $this->formatCurrency($invoice->currency, ($invoiceData?->tax / 100)));
                            //Buyer Details
                            $buyer = [
                                'name' => auth()->user()->name,
                                'email' => auth()->user()->email,
                            ];
                            $pdfData->put('buyer', $buyer);
                            //Logo Details
                            $logoUrl = $this->getLogo();
                            $path = $logoUrl;
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $logoData = file_get_contents($path);
                            $logo = 'data:image/' . $type . ';base64,' . base64_encode($logoData);
                            $pdfData->put('logo', $logo);

                            //Plan Details
                            $plan = $record->plan;
                            $planDetails = [
                                [
                                    'name' => $plan?->name,
                                    'quantity' => 1,
                                    'price' => $this->formatCurrency($invoice->currency, ($invoiceData?->subtotal / 100)),
                                    'total' => $this->formatCurrency($invoice->currency, ($invoiceData?->subtotal / 100)),

                                ]
                            ];
                            $pdfData->put('items', $planDetails);
                            $data = [
                                'invoice' => $pdfData
                            ];

                            $pdf = Pdf::loadView('pdf-templates.invoice', $data);
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->stream();
                            }, 'invoice_' . $invoice->invoice_id . '.pdf');
                        } catch (\Throwable $throwable) {
                            // Error
                            Notification::make()
                                ->title(__('messages.t_toast_something_went_wrong'))
                                ->body($throwable->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
            ])
            ->headerActions([
                Action::make('refresh')
                    ->label(__('messages.t_subscription_refresh'))
                    ->tooltip(__('messages.t_subscription_refresh_tooltip'))
                    ->color('success')
                    ->action(fn() => redirect()->back())
            ])
            ->emptyState(view('tables.empty-state', ['message' => __('messages.t_no_subscriptions')]))
            ->filters([]);
    }

    protected function updateSubscriptionCancelStatus($record, $status)
    {
        if ($record->payment_method == 'stripe') {
            $stripe = new \Stripe\StripeClient(app(StripeSettings::class)->secret_key);
            $stripe->subscriptions->update(
                $record->subscription_reference,
                ['cancel_at_period_end' => $status]
            );
        } else if ($record->payment_method == 'paypal') {
            $paypalSettings = app(PaypalSettings::class);

            // Set common PayPal credentials
            $credentials = [
                'client_id'     => $paypalSettings->client_id,
                'client_secret' => $paypalSettings->client_secret,
                'app_id'        => '',
            ];

            // Set gateway config
            $config = [
                'mode' => $paypalSettings->mode, // or 'live', consider making this dynamic
                'sandbox' => $credentials,
                'live'    => $credentials,
                'payment_action' => 'Sale',
                'currency'       => $paypalSettings->currency,
                'notify_url'     => '/', // Use a route helper
                'locale'         => 'en_US',
                'validate_ssl'   => true,
            ];

            // Initialize and configure PayPal provider
            $provider = new PayPalClient($config);
            $provider->setApiCredentials($config);
            $provider->getAccessToken();

            $provider->cancelSubscription(
                $record->subscription_reference,
                'Choose another plan'
            );

            $record->cancels_at = Carbon::now();
            $record->save();
        }
    }

    public function formatCurrency($currency, $amount)
    {
        $paymentSettings = app(PaymentSettings::class);
        return \Number::currency(floatval($amount), $currency, locale: $paymentSettings->currency_locale);
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

    public function getSubscriptionSettingsProperty()
    {
        return app(SubscriptionSettings::class);
    }
    /**
     * Renders the MyPackages view.
     */
    public function render()
    {
        return view('livewire.user.my-subscription-detail');
    }
}
