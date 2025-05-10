<?php

namespace App\Traits;

use App\Settings\PaypalSettings;
use Exception;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

trait PaypalSubscription
{
    public $provider;

    public function initializeConfig()
    {
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
        $this->provider = new PayPalClient($config);
        $this->provider->setApiCredentials($config);
        $this->provider->getAccessToken();
    }

    public function createPlan($plan)
    {
        try {

           
            $this->initializeConfig();
            $responseForProduct = $this->provider->createProduct([
                "name" => $plan->name,
                "description" => $plan->description
            ]);

            // initially we implement month based
            $intervalUnit =   'MONTH';
            // $intervalUnit =  $plan->frequency == 'monthly' ? "MONTH" : 'YEAR';

            $amount = bcdiv($plan->price, 1, 2);

            $billing_cycles = [];

            $billing_cycles[] = [
                "tenure_type" => "REGULAR",
                "sequence" => sizeof($billing_cycles) + 1,
                "frequency" => [
                    "interval_unit" => $intervalUnit,
                    "interval_count" => 1
                ],
                "total_cycles" => 12,
                "pricing_scheme" => [
                    "fixed_price" => [
                        "value" => $amount,
                        "currency_code" =>  getPaypalCurrency()
                    ]
                ]
            ];

            $planData = [
                "product_id" => $responseForProduct['id'],
                "name" => $plan->name,
                "billing_cycles" => $billing_cycles,
                "payment_preferences" => [
                    "auto_bill_outstanding" => true,
                    "setup_fee" => [
                        "value" => $amount,
                        "currency_code" =>  getPaypalCurrency()
                    ],
                    "setup_fee_failure_action" => "CONTINUE",
                    "payment_failure_threshold" => 3
                ],
                "description" => $plan->description,
                "status" => "ACTIVE",
                "taxes" => [
                    "percentage" => 0,
                    "inclusive" => false
                ]
            ];

            $responseForPlan = $this->provider->createPlan($planData);

            $plan->update([
                'paypal_plan_id' => $responseForPlan['id']
            ]);

            return $plan;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
        }
    }

    public function createWebHook()
    {
        try {
            $this->initializeConfig();
            
            $data = ["*"];
            $url = route('paypal.webhook');

            $webhook = $this->provider->createWebHook($url, $data);

            $this->info(json_encode($webhook, true));
        } catch (Exception $ex) {
            $this->error($ex->getMessage());
        }
    }
}
