<?php

namespace App\Livewire\Reservation;

use App\Models\Reservation\Cart;
use App\Models\Reservation\Location;
use App\Models\Reservation\TemporaryOrder;
use App\Traits\HelperTraits;
use App\Settings\GeneralSettings;
use App\Settings\OfflinePaymentSettings;
use App\Settings\PaymentSettings;
use App\Settings\SEOSettings;
use Filament\Forms\Components\Radio;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Attributes\Url;
use Livewire\Component;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

class CheckoutSummary extends Component implements HasForms
{
    use  InteractsWithForms, HelperTraits, SEOToolsTrait;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';

    public $carts;
    public ?array $data = [];
    public $isModalOpen = false;
    public $locations;
    public $deliveryAddress;
    public $totalAmount = 0;
    public $subtotalAmount = 0;
    public $payment_method;
    public $enableCashOnDelivery = true;
    public $selectedPaymentMethod;
    public $selectedPaymentInstruction;

    protected $paymentGateways = [
        'stripe' => 'e-commerce::payment.stripe',
        'paypal' => 'e-commerce::payment.paypal',
        'flutterwave' => 'e-commerce::payment.flutterwave',
        'paymongo' => 'e-commerce::payment.paymongo',
        'razorpay' => 'e-commerce::payment.razorpay',
        'paystack' => 'e-commerce::payment.paystack',
        'offline' => 'offline-payment',
    ];

    public function mount()
    {
        $temporaryOrder = session()->get('current_temporary_order');
        $temporaryOrder = TemporaryOrder::find($temporaryOrder);
        if(!$temporaryOrder){
            abort(404);
        }
        $this->carts = auth()->user()->carts()->whereIn('id', $temporaryOrder->items)->get();

        $this->locations = auth()->user()->locations()->where('type', 'delivery_address')->get();

        if ($this->locations->isEmpty()) {
            session()->put('delivery-address', null);
        } else if ($this->locations->isNotEmpty() && !session()->has('delivery-address')) {
            session()->put('delivery-address', $this->locations[0]);
        }

        $this->carts->each(function ($cart) {
            $this->subtotalAmount = $this->subtotalAmount + $cart->quantity * (($cart->ad->isEnabledOffer() && $cart->ad->offer_price) ? $cart->ad->offer_price : $cart->ad->price);

            // if (!$cart->ad->enable_cash_on_delivery) {
            //     $this->enableCashOnDelivery = false;
            // }
        });

        if (!session()->get('current_temporary_order') && !session()->get('current_total_amount')) {
            $temporaryOrder = TemporaryOrder::create([
                'user_id' => auth()->id(),
                'items' => $this->carts->pluck('id'),
                'total_amount' => $this->totalAmount,
                'status' => 'order_created',
                'shipping_address_id' => $this->deliveryAddress->id

            ]);

            session()->put('current_temporary_order', $temporaryOrder->id);
            session()->put('current_total_amount', $temporaryOrder->total_amount);
        }

        $this->deliveryAddress = session()->get('delivery-address',  null);

        $this->payment_gateway_params['order_id'] = session()->get('current_temporary_order');

        $paymentOptions = $this->initializePaymentOptions();

        if (count($paymentOptions) == 1) {
            $keys = array_keys($paymentOptions);  // This will return an array of keys $keys[0];

            $this->payment_method = $keys[0];
            $this->currentPayment = $this->paymentGateways[$this->payment_method];

            $this->updatePaymentData();
        }
        $this->updatePaymentData();

        $this->setSeoData();
    }

    public function getPaymentDataProperty()
    {
        return [
            'subtotal' => $this->subtotalAmount,
            'total' => $this->totalAmount
        ];
    }

    public function locationForms(Form $form): Form
    {
        return $form->schema($this->locationForm())->statePath('data');
    }

    public function paymentOptionForm(Form $paymentOptionForm): Form
    {
        $paymentOptions = $this->initializePaymentOptions();

        return $paymentOptionForm
            ->schema([
                Radio::make('payment_method')
                    ->hiddenLabel()
                    ->live()
                    ->options($paymentOptions)
                    ->afterStateUpdated(function ($state) {
                        if (app('filament')->hasPlugin('offline-payment') && app(OfflinePaymentSettings::class)->status && $this->enableCashOnDelivery) {
                            $this->selectedPaymentMethod = str_replace('offline_', '', $state);

                            // Filter the payment types and find the matching one
                            $filtered = array_filter(app(OfflinePaymentSettings::class)->payment_type, function ($item) {
                                return $item['name'] == $this->selectedPaymentMethod;
                            });

                            // If a match is found, update the selected payment instruction
                            foreach ($filtered as $item) {
                                $this->selectedPaymentInstruction = $item['instruction'];
                            }
                        }
                            // Set current payment method
                            $this->currentPayment = $this->paymentGateways[$state] ?? null;


                        $this->updatePaymentData();
                    })
            ]);
    }

    protected function updatePaymentData()
    {
        // Accessing PaymentSettings
        $paymentSettings = app(PaymentSettings::class);

        if(!isEnablePointSystem() && isECommerceTaxOptionEnabled() && is_ecommerce_active())
        {
            $this->tax = ($this->subtotalAmount * getECommerceTaxRate()) / 100;
        }
        else {
            // No tax applied
            $this->tax = 0;
        }
        // Add tax calculation logic here if necessary
        $this->totalAmount = $this->subtotalAmount + $this->tax;

        $this->defaultCurrency =  $paymentSettings->currency;
        $paymentGatewayRate = $this->getPaymentGatewayRate();

        $systemExchangeRate = app(PaymentSettings::class)->exchange_rate;

        $this->isDifferentRate = $paymentGatewayRate != 1.0 && $paymentGatewayRate != $systemExchangeRate;

        $this->convertedTotal = $this->totalAmount * $paymentGatewayRate / $systemExchangeRate;
    }

    protected function getForms(): array
    {
        return [
            'locationForms',
            'paymentOptionForm',
        ];
    }

    public function selectAddress($addressId)
    {
        $this->helperSelectAddress($addressId);
    }

    public function addAddress(): void
    {
        $this->helperAddAddress($this->locationForms);
    }

    public function offlinePaymentOrderNow()
    {
        return redirect()->route('reservation.payment-callback.offline', [
            'temporaryOrderId' => session()->get('current_temporary_order'),
            'payment_method' => $this->payment_method,
        ]);
    }

    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings     = app(SEOSettings::class);

        $title = __('messages.t_checkout_summary') . ' ' . ($generalSettings->separator ?? '-') . ' ' .
                 ($generalSettings->site_name ?? config('app.name'));
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($seoSettings->meta_description);
    }

    public function render()
    {
        return view('livewire.reservation.checkout-summary');
    }
}
