<?php

namespace App\Livewire\Ad\PostAd;

use App\Settings\{
    MollieSettings,
    PaystackSettings,
    RazorpaySettings,
    StripeSettings,
    PaypalSettings,
    PaymentSettings,
    FlutterwaveSettings,
    OfflinePaymentSettings,
    PaymongoSettings,
    CmiSettings,
    PayuSettings,
    PhonePeSettings
};
use App\Models\Promotion;
use Akaunting\Money\Currency as AkauntingCurrency;
use Filament\Forms\Components\Radio;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class PaymentAd extends Component implements HasForms
{
    use InteractsWithForms;

    public $currentPayment;
    public $payment_method;
    public $promotionIds;
    public $type = 'UG';
    public $promotions;
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;
    public $isDifferentRate = false;
    public $convertedTotal = 0;
    public $defaultCurrency;
    public $selectedPaymentMethod;
    public $selectedPaymentInstruction;
    public $id;

    protected $paymentGateways = [
        'stripe' => 'payment.stripe-payment',
        'paypal' => 'paypal-payment',
        'flutterwave' => 'flutterwave-payment',
        'offline' => 'offline-payment',
        'paymongo' => 'paymongo-payment',
        'razorpay' => 'razorpay-payment',
        'paystack' => 'paystack-payment',
        'phonepe' => 'phonepe-payment',
        'payu' => 'payu-payment',
        'mollie' => 'mollie-payment',
        'cmi' => 'cmi-payment',
    ];

    public function mount($id)
    {
        $this->id = $id;
        $this->initializePaymentOptions();
        $this->updatePaymentData();
    }

    protected function updatePaymentData()
    {
        $this->promotions = Promotion::whereIn('id', $this->promotionIds)->get();
        $this->subtotal = $this->promotions->sum('price');

        $paymentSettings = app(PaymentSettings::class);

        $this->tax = $paymentSettings->enable_tax
            ? ($paymentSettings->tax_type === 'percentage'
                ? ($this->subtotal * $paymentSettings->tax_rate) / 100
                : $paymentSettings->tax_rate)
            : 0;

        $this->total = $this->subtotal + $this->tax;
        $this->defaultCurrency = $paymentSettings->currency;

        $paymentGatewayRate = $this->getPaymentGatewayRate();
        $systemExchangeRate = $paymentSettings->exchange_rate;

        $this->isDifferentRate = $paymentGatewayRate !== 1.0 && $paymentGatewayRate !== $systemExchangeRate;
        $this->convertedTotal = $this->total * $paymentGatewayRate / $systemExchangeRate;
    }

    public function getPaymentDataProperty()
    {
        return [
            'user_id' => auth()->id(),
            'ad_id' => $this->id,
            'promotionIds' => $this->promotionIds,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total
        ];
    }

    private function getPaymentGatewayRate()
    {
        $settings = [
            'stripe' => StripeSettings::class,
            'paypal' => PaypalSettings::class,
            'flutterwave' => FlutterwaveSettings::class,
            'offline' => OfflinePaymentSettings::class,
            'paymongo' => PaymongoSettings::class,
            'paystack' => PaystackSettings::class,
            'razorpay' => RazorpaySettings::class,
            'mollie' => MollieSettings::class,
            'cmi' => CmiSettings::class,
            'phonepe' => PhonePeSettings::class,
            'payu' => PayuSettings::class,
        ];

        return isset($settings[$this->payment_method])
            ? app($settings[$this->payment_method])->exchange_rate
            : 1.0;
    }

    public function getExchangeCurrencySymbol()
    {
        $settings = [
            'stripe' => StripeSettings::class,
            'paypal' => PaypalSettings::class,
            'flutterwave' => FlutterwaveSettings::class,
            'offline' => OfflinePaymentSettings::class,
            'paymongo' => PaymongoSettings::class,
            'paystack' => PaystackSettings::class,
            'razorpay' => RazorpaySettings::class,
            'mollie' => MollieSettings::class,
            'cmi' => CmiSettings::class,
            'phonepe' => PhonePeSettings::class,
        ];

        return isset($settings[$this->payment_method])
            ? (new AkauntingCurrency(app($settings[$this->payment_method])->currency))->getSymbol()
            : '$';
    }

    protected function initializePaymentOptions()
    {
        $paymentOptions = collect([
            'stripe' => StripeSettings::class,
            'paypal' => PaypalSettings::class,
            'flutterwave' => FlutterwaveSettings::class,
            'paymongo' => PaymongoSettings::class,
            'razorpay' => RazorpaySettings::class,
            'paystack' => PaystackSettings::class,
            'mollie' => MollieSettings::class,
            'phonepe' => PhonePeSettings::class,
            'cmi' => CmiSettings::class,
            'payu' => PayuSettings::class,
        ])->filter(function ($settingClass) {
            return app($settingClass)->status;
        })->mapWithKeys(function ($settingClass, $key) {
            return [$key => app($settingClass)->name];
        })->toArray();

        if (app('filament')->hasPlugin('offline-payment') && app(OfflinePaymentSettings::class)->status) {
            // $paymentOptions['offline'] = app(OfflinePaymentSettings::class)->name;
            $offlineSettings = app(OfflinePaymentSettings::class);
            if (!empty($offlineSettings->payment_type)) {
                foreach ($offlineSettings->payment_type as $type) {
                    $key = 'offline_' . $type['name'];
                    $paymentOptions[$key] = $type['name'];
                    $this->paymentGateways[$key] = 'offline-payment'; // Add to paymentGateways
                }
            }
        }

        if (count($paymentOptions) === 1) {
            $defaultMethod = array_key_first($paymentOptions);
            $this->payment_method = $defaultMethod;
            $this->selectedPaymentMethod = $defaultMethod;
            $this->currentPayment = $this->paymentGateways[$defaultMethod];
        }
        return $paymentOptions;
    }

    public function form(Form $form): Form
    {
        $paymentOptions = $this->initializePaymentOptions();

        return $form->schema([
            Radio::make('payment_method')
                ->hiddenLabel()
                ->live()
                ->options($paymentOptions)
                ->afterStateUpdated(function ($state) {
                    $this->handleStateUpdate($state);
                }),
        ]);
    }

    private function handleStateUpdate($state)
    {
        if (str_starts_with($state, 'offline_')) {
            $this->handleOfflinePayment($state);
        }

        $this->currentPayment = $this->paymentGateways[$state] ?? null;
        $this->updatePaymentData();
    }

    private function handleOfflinePayment($state)
    {
        $offlineSettings = app(OfflinePaymentSettings::class);

        $this->selectedPaymentMethod = str_replace('offline_', '', $state);
        $filtered = collect($offlineSettings->payment_type)->firstWhere('name', $this->selectedPaymentMethod);

        if ($filtered) {
            $this->selectedPaymentInstruction = $filtered['instruction'];
            $this->dispatch('post-created', [
                'name' => $this->selectedPaymentMethod,
                'instruction' => $this->selectedPaymentInstruction
            ]);
        }
    }

    public function render()
    {
        return view('livewire.ad.post-ad.payment-ad');
    }
}
