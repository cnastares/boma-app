<?php

namespace App\Http\Controllers\Reservation;

use App\Models\Reservation\Cart;
use App\Models\Reservation\Location;
use App\Models\Reservation\TemporaryOrder;
use App\Traits\Reservation\OrderHelperTraits;
use App\Http\Controllers\Controller;
use App\Settings\FlutterwaveSettings;
use Illuminate\Http\Request;
use App\Settings\PaymongoSettings;
use App\Settings\PaypalSettings;
use App\Settings\StripeSettings;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Http;

class PaymentCallbackController extends Controller
{
    use OrderHelperTraits;

    private $stripeSettings;
    private $paymongoSettings;
    private $paypalSettings;
    private $flutterwaveSettings;

    public function stripe(Request $request, $temporaryOrderId)
    {
        $this->stripeSettings = app(StripeSettings::class);
        $transactionId = $request->get('payment_intent');

        if ($transactionId && $this->verifyStripe($transactionId)['success']) {
            $temporaryOrder = TemporaryOrder::where('user_id', auth()->id())
                ->where('id', $temporaryOrderId)
                ->firstOrFail(); // Use firstOrFail for better error handling

            $carts = Cart::whereIn('id', $temporaryOrder->items)->get();
            $shippingAddress = Location::find($temporaryOrder->shipping_address_id);

            $this->createOrder($carts, 'completed', 'stripe', $shippingAddress, $transactionId);

            return redirect()->route('reservation.order-confirmation');
        }

        return redirect('/')->withErrors('Payment verification failed or transaction ID missing.');
    }


    private function verifyStripe($transactionId)
    {
        $stripe = new \Stripe\StripeClient($this->stripeSettings->secret_key);
        $payment = $stripe->paymentIntents->retrieve($transactionId, []);

        if ($payment && $payment->status === 'succeeded') {
            return ['success' => true, 'response' => $payment];
        }

        return ['success' => false, 'message' => __('messages.t_error_payment_failed')];
    }

    public function paypal(Request $request, $temporaryOrderId)
    {
        $this->paypalSettings = app(PaypalSettings::class);
        // Get transaction id
        $transactionId = $request->order;
        // Check webhook event
        if ($transactionId) {
            $response = $this->verifyPaypal($transactionId);

            if (is_array($response) && $response['success'] == TRUE) {
                // Get order id
                // $orderId = $response['response']['purchase_units'][0]['payments']['captures'][0]['invoice_id'];

                $temporaryOrder = TemporaryOrder::where('user_id', auth()->id())
                    ->where('id', $temporaryOrderId)
                    ->firstOrFail(); // Use firstOrFail for better error handling

                $carts = Cart::whereIn('id', $temporaryOrder->items)->get();
                $shippingAddress = Location::find($temporaryOrder->shipping_address_id);

                $this->createOrder($carts, 'completed', 'paypal', $shippingAddress, $transactionId);

                return redirect()->route('reservation.order-confirmation');
            }
        }

        return redirect('/');
    }

    /**
     * Verify if payment succeeded
     *
     * @param string $id
     * @return array
     */
    private function verifyPaypal($id)
    {
        try {
            // Get payment gateway keys
            $client_id     = $this->paypalSettings->client_id;
            $client_secret = $this->paypalSettings->client_secret;
            // Set gateway config
            $config = [
                'mode' => $this->paypalSettings->mode,
                'live' => [
                    'client_id'     => $client_id,
                    'client_secret' => $client_secret,
                    'app_id'        => '',
                ],
                'sandbox' => [
                    'client_id'     => $client_id,
                    'client_secret' => $client_secret,
                    'app_id'        => '',
                ],
                'payment_action' => 'Sale',
                'currency'       => $this->paypalSettings->currency,
                'notify_url'     => 'https://your-site.com/paypal/notify',
                'locale'         => 'en_US',
                'validate_ssl'   => true,
            ];


            // Set paypal provider and config
            $client = new PayPalClient($config);
            // Set client credentials
            $client->setApiCredentials($config);
            // Get paypal access token
            $client->getAccessToken();
            // Capture this order
            $order  = $client->capturePaymentOrder($id);

            // Check if payment succeeded
            if (is_array($order) && isset($order['status']) && $order['status'] === 'COMPLETED') {
                // Done
                return [
                    'success'  => true,
                    'response' => $order
                ];
            } else {
                // Failed
                return [
                    'success' => false,
                    'message' => __('messages.t_error_payment_failed')
                ];
            }
        } catch (\Throwable $th) {
            // Error
            return [
                'success' => false,
                'message' => __('messages.t_toast_something_went_wrong')
            ];
        }
    }

    public function flutterwave(Request $request, $temporaryOrderId)
    {
        $this->flutterwaveSettings = app(FlutterwaveSettings::class);

        $transactionId = $request->get('transaction_id');
        // Check webhook event
        if ($transactionId) {

            $response = $this->verifyFlutterwave($transactionId);

            if (is_array($response) && $response['success'] == TRUE) {
                $temporaryOrder = TemporaryOrder::where('user_id', auth()->id())
                    ->where('id', $temporaryOrderId)
                    ->firstOrFail();

                $carts = Cart::whereIn('id', $temporaryOrder->items)->get();
                $shippingAddress = Location::find($temporaryOrder->shipping_address_id);

                $this->createOrder($carts, 'completed', 'paypal', $shippingAddress, $transactionId);

                return redirect()->route('reservation.order-confirmation');
            }
        }

        return redirect('/');
    }


    /**
     * Verify if payment succeeded
     *
     * @param string $id
     * @return array
     */
    private function verifyFlutterwave($id)
    {
        try {
            // Get payment gateway keys
            $secret_key = $this->flutterwaveSettings->secret_key;

            $response   = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secret_key,
                'Accept'        => 'application/json',
            ])->get("https://api.flutterwave.com/v3/transactions/$id/verify")->json();


            // Check if payment succeeded
            if (is_array($response) && $response['status'] === 'success') {

                // Done
                return [
                    'success'  => true,
                    'response' => $response
                ];
            } else {

                // Failed
                return [
                    'success' => false,
                    'message' => __('messages.t_error_payment_failed')
                ];
            }
        } catch (\Throwable $th) {

            // Error
            return [
                'success' => false,
                'message' => __('messages.t_toast_something_went_wrong')
            ];
        }
    }

    public function paymongo(Request $request, $temporaryOrderId)
    {
        $this->paymongoSettings = app(PaymongoSettings::class);

        // Get transaction id
        $transactionId = session('paymongo_checkout_id');

        // Check webhook event
        if ($transactionId) {

            $response = $this->verifyPaymongo($transactionId);

            if (is_array($response) && $response['success'] == TRUE) {
                // Get order id
                $order_id = $response['response']['attributes']['reference_number'];

                // Check If Package Management Callback
                $temporaryOrder = TemporaryOrder::where('user_id', auth()->id())
                    ->where('id', $temporaryOrderId)
                    ->firstOrFail(); // Use firstOrFail for better error handling

                $carts = Cart::whereIn('id', $temporaryOrder->items)->get();
                $shippingAddress = Location::find($temporaryOrder->shipping_address_id);

                $this->createOrder($carts, 'completed', 'stripe', $shippingAddress, $transactionId);

                return redirect()->route('reservation.order-confirmation');
            }
        }

        return redirect('/');
    }

    private function verifyPaymongo($id)
    {
        try {
            $client = new \GuzzleHttp\Client();
            // Get payment gateway keys
            $response = $client->request('GET', 'https://api.paymongo.com/v1/checkout_sessions/' . $id, [
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => 'Basic c2tfdGVzdF9RdkdTb01BWjhFUkduNEtxOHlkM1FiSjI6',
                ],
            ]);
            $data = json_decode($response->getBody(), true);

            $returnResponse = $data['data']['attributes']['payments'][0]['attributes']['status'];

            // Check if payment succeeded
            if ($returnResponse === 'paid') {
                // Done
                return [
                    'success'  => true,
                    'response' => $data['data']
                ];
            } else {

                // Failed
                return [
                    'success' => false,
                    'message' => __('messages.t_error_payment_failed')
                ];
            }
        } catch (\Throwable $th) {

            // Error
            return [
                'success' => false,
                'message' => __('messages.t_toast_something_went_wrong')
            ];
        }
    }

    public function offline($temporaryOrderId, $payment_method)
    {
        $temporaryOrder = TemporaryOrder::where('user_id', auth()->id())
            ->where('id', $temporaryOrderId)
            ->firstOrFail(); // Use firstOrFail for better error handling

        $carts = Cart::whereIn('id', $temporaryOrder->items)->get();
        $shippingAddress = Location::find($temporaryOrder->shipping_address_id);

        $this->createOrder($carts, 'completed', $payment_method, $shippingAddress, null);

        return redirect()->route('reservation.order-confirmation');
    }

    public function pointBasedOrder($temporaryOrderId)
    {
        $temporaryOrder = TemporaryOrder::where('user_id', auth()->id())
            ->where('id', $temporaryOrderId)
            ->firstOrFail(); // Use firstOrFail for better error handling

        $carts = Cart::whereIn('id', $temporaryOrder->items)->get();
        $shippingAddress = Location::find($temporaryOrder->shipping_address_id);

        $this->createOrder($carts, 'pending', 'points', $shippingAddress, null, RESERVATION_TYPE_POINT_VAULT);

        return redirect()->route('reservation.order-confirmation');
    }
}
