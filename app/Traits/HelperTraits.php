<?php

namespace App\Traits;

use App\Models\Reservation\Location;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Settings\FlutterwaveSettings;
use App\Settings\OfflinePaymentSettings;
use App\Settings\PaymentSettings;
use App\Settings\PaymongoSettings;
use App\Settings\PaypalSettings;
use App\Settings\PaystackSettings;
use App\Settings\RazorpaySettings;
use App\Settings\StripeSettings;
use Carbon\Carbon;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Notifications\Notification;

trait HelperTraits
{
    public $defaultCurrency;
    public $tax;
    public $isDifferentRate;
    public $convertedTotal;
    public $currentPayment;
    public $payment_gateway_params;

    public function locationForm()
    {
        return [
            Group::make()->schema([
                TextInput::make('name')
                    ->label(__('messages.t_name'))
                    ->required(),
                TextInput::make('phone_number')
                    ->label(__('messages.t_phone_number'))
                    ->numeric()
                    ->required(),
                TextInput::make('house_number')
                    ->label(__('messages.t_house_number'))
                    ->required(),
                TextInput::make('address')
                    ->label(__('messages.t_address'))
                    ->required(),
                Select::make('country_id')
                    ->label(__('messages.t_country'))
                    ->options(Country::orderBy('name')->pluck('name', 'id')->toArray())
                    ->live()
                    ->afterStateUpdated(fn(callable $set) => $set('state_id', null))
                    ->required(),
                Select::make('state_id')
                        ->label(__('messages.t_state'))
                        ->options(function (Get $get) {
                        $countryId = $get('country_id');
                        if (!$countryId) {
                            return [];
                        }
                        return State::where('country_id', $countryId)->orderBy('name')->pluck('name', 'id')->toArray();
                    })
                    ->live()
                    ->hidden(fn(Get $get): bool => !$get('country_id'))
                    ->afterStateUpdated(fn(callable $set) => $set('city_id', null))
                    ->required(),
                Select::make('city_id')
                    ->label(__('messages.t_city'))
                    ->options(function (Get $get) {
                        $stateId = $get('state_id');
                        if (!$stateId) {
                            return [];
                        }
                        return City::where('state_id', $stateId)->orderBy('name')->pluck('name', 'id')->toArray();
                    })
                    ->hidden(fn(Get $get): bool => !$get('state_id'))
                    ->required(),
                TextInput::make('postal_code')
                    ->label(__('messages.t_postal_code'))
                    ->required(),

            ])->columns(2)
        ];
    }

    public function helperSelectAddress($addressId)
    {
        $location = Location::find($addressId);
        session()->put('delivery-address',  $location);

        $this->deliveryAddress = session()->get('delivery-address',  null);
    }

    public function helperAddAddress($form)
    {
        $data = $form->getState();
        $data['type'] = 'delivery_address';
        $data['user_id'] = auth()->id();

        $location = Location::create($data);
        $this->locations = auth()->user()->locations()->where('type', 'delivery_address')->get();

        session()->put('delivery-address',  $location);
        $this->deliveryAddress = session()->get('delivery-address',  null);

        $this->dispatch('close-modal', id: 'add-address');

        Notification::make()
            ->title(__('messages.t_saved_successfully'))
            ->success()
            ->send();

        $this->form->fill([]);
    }

    public function initializePaymentOptions()
    {
        $paymentOptions = [];

        if (app(StripeSettings::class)->status) {
            $paymentOptions['stripe'] = app(StripeSettings::class)->name;
        }

        if (app('filament')->hasPlugin('paypal') && app(PaypalSettings::class)->status) {
            $paymentOptions['paypal'] = app(PaypalSettings::class)->name;
        }

        if (app('filament')->hasPlugin('flutterwave') && app(FlutterwaveSettings::class)->status) {
            $paymentOptions['flutterwave'] = app(FlutterwaveSettings::class)->name;
        }

        if (app('filament')->hasPlugin('paymongo') && app(PaymongoSettings::class)->status) {
            $paymentOptions['paymongo'] = app(PaymongoSettings::class)->name;
        }

        if (app('filament')->hasPlugin('razorpay') && app(RazorpaySettings::class)->status) {
            $paymentOptions['razorpay'] = app(RazorpaySettings::class)->name;
        }

        if (app('filament')->hasPlugin('paystack') && app(PaystackSettings::class)->status) {
            $paymentOptions['paystack'] = app(PaystackSettings::class)->name;
        }

        if (app('filament')->hasPlugin('offline-payment') && app(OfflinePaymentSettings::class)->status && $this->enableCashOnDelivery) {
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

        // Set default payment method if only one option is enabled
        if (count($paymentOptions) === 1) {
            $defaultMethod = array_key_first($paymentOptions);

            $this->payment_method = $defaultMethod;
            $this->currentPayment = $this->paymentGateways[$defaultMethod];
        }

        return $paymentOptions;
    }

    private function getPaymentGatewayRate()
    {
        return match ($this->payment_method) {
            'stripe' => app(StripeSettings::class)->exchange_rate,
            'paypal' => app(PaypalSettings::class)->exchange_rate,
            'flutterwave' => app(FlutterwaveSettings::class)->exchange_rate,
            'offline' => app(OfflinePaymentSettings::class)->exchange_rate,
            'paymongo' => app(PaymongoSettings::class)->exchange_rate,
            'paystack' => app(PaystackSettings::class)->exchange_rate,
            'razorpay' => app(RazorpaySettings::class)->exchange_rate,
            default => 1.0
        };
    }

    public function getPaymentSettingsProperty()
    {
        return app(PaymentSettings::class);
    }
}
