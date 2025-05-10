<?php

namespace App\Livewire\Payment;

use App\Models\WebhookPackage;
use Livewire\Component;
use App\Models\WebhookUpgrade;
use Stripe\StripeClient;
use App\Settings\StripeSettings;
use Filament\Notifications\Notification;

class StripePayment extends Component
{
    public $id;

    public $total;

    public $tax;

    public $type;

    public $data;

    public $subtotal;

    public $public_key;

    public $payment_gateway_params = [];

    /**
     * Mount the component and set the properties if an ad ID is provided.
     */
    public function mount($id)
    {
        $this->id = $id;
        $this->public_key = app(StripeSettings::class)->public_key;
        $this->processPayment();
    }

    public function processPayment()
    {
        $total = $this->total;

        // Initialize Stripe
        $stripe = new StripeClient(app(StripeSettings::class)->secret_key); // Replace with your Stripe secret key

        try {

            // Get the logged-in user
            $user = auth()->user();

             // Create a new customer in Stripe
             $customer = $stripe->customers->create([
                'name'  => $user->name,
                'email' => $user->email,
            ]);

            // Create a payment intent
            $intent = $stripe->paymentIntents->create([
                'amount' => $total * 100, // total in cents
                'currency' => app(StripeSettings::class)->currency, // Replace with your desired currency
                'payment_method_types' => ['card'],
                'customer' => $customer?->id ?? null,
            ]);

            $this->payment_gateway_params['client_secret'] = $intent->client_secret;

            $this->dispatch('post-created');
            $this->handleWebhookUpgrade($intent);

        } catch (\Throwable $throwable) {

            Notification::make()
                ->title(__('messages.t_error_payment_failed'))
                ->danger()
                ->body($throwable->getMessage())
                ->send();
        }

    }

    protected function handleWebhookUpgrade($intent)
    {
        try {
            if ($this->type == 'PKG') {
                WebhookPackage::create([
                    'data' => json_encode($this->data),
                    'payment_id' => $intent->id,
                    'payment_method' => 'stripe',
                    'status' => 'pending'
                ]);
            } else {
                WebhookUpgrade::create([
                    'data' => json_encode($this->data),
                    'payment_id' => $intent->id,
                    'payment_method' => 'stripe',
                    'status' => 'pending'
                ]);
            }
        } catch (\Throwable $th) {
            // Handle any exceptions
        }
    }

    public function render()
    {
        return view('livewire.payment.stripe-payment');
    }
}
