<?php

namespace App\Http\Controllers\Callback;

use App\Http\Controllers\Controller;
use App\Traits\HandlesPackageApplication;
use App\Traits\HandlesPromotionApplication;
use App\Traits\LogsActivity;
use App\Models\WebhookUpgrade;
use App\Models\WebhookPackage;
use Illuminate\Http\Request;
use App\Settings\StripeSettings;


class StripeController extends Controller
{
    use HandlesPackageApplication, HandlesPromotionApplication, LogsActivity;

    private $stripeSettings;

    /**
     * Handle the callback from the Stripe payment gateway.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        return $this->executeWithLogging($request, 'callback', function ($traceId) use ($request) {
            $this->stripeSettings = app(StripeSettings::class);

            $transactionId = $request->get('payment_intent');
            $action = $request->get('action');

            $this->logPaymentEvent($traceId, 'stripe_callback_received', [
                'payment_intent' => $transactionId,
                'action' => $action,
                'query_params' => $request->query()
            ]);

            if (!$transactionId || !$action) {
                $this->logWarning($traceId, 'callback', 'Missing required parameters', [
                    'missing_payment_intent' => !$transactionId,
                    'missing_action' => !$action
                ]);
                return redirect('/');
            }

            try {
                $response = $this->verifyPayment($transactionId, $traceId);
                
                if (!$response['success']) {
                    $this->logPaymentEvent($traceId, 'stripe_payment_verification_failed', [
                        'payment_intent' => $transactionId,
                        'error_message' => $response['message'] ?? 'Unknown error'
                    ]);
                    return redirect('/');
                }

                $this->logPaymentEvent($traceId, 'stripe_payment_verified', [
                    'payment_intent' => $transactionId,
                    'action' => $action
                ]);

                if ($action == 'PKG') {
                    return $this->handlePackagePayment($transactionId, $traceId);
                } else {
                    return $this->handleUpgradePayment($transactionId, $traceId);
                }

            } catch (\Exception $e) {
                $this->logPaymentEvent($traceId, 'stripe_callback_error', [
                    'payment_intent' => $transactionId,
                    'action' => $action,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }

    private function handlePackagePayment($transactionId, $traceId)
    {
        try {
            $orderData = WebhookPackage::where('payment_id', $transactionId)
                ->where('payment_method', 'stripe')
                ->where('status', 'pending')
                ->firstOrFail();

            $this->logPaymentEvent($traceId, 'package_order_found', [
                'order_id' => $orderData->id,
                'payment_intent' => $transactionId,
                'user_id' => $orderData->user_id ?? null
            ]);

            $routeParameters = $this->applyPackages($orderData);

            $this->logPaymentEvent($traceId, 'package_applied_successfully', [
                'order_id' => $orderData->id,
                'route_parameters' => $routeParameters
            ]);

            return redirect()->route('package-success', $routeParameters);

        } catch (\Exception $e) {
            $this->logPaymentEvent($traceId, 'package_processing_failed', [
                'payment_intent' => $transactionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function handleUpgradePayment($transactionId, $traceId)
    {
        try {
            $orderData = WebhookUpgrade::where('payment_id', $transactionId)
                ->where('payment_method', 'stripe')
                ->where('status', 'pending')
                ->firstOrFail();

            $this->logPaymentEvent($traceId, 'upgrade_order_found', [
                'order_id' => $orderData->id,
                'payment_intent' => $transactionId,
                'user_id' => $orderData->user_id ?? null
            ]);

            $routeParameters = $this->applyPromotionsWithRedirect($orderData);

            $this->logPaymentEvent($traceId, 'upgrade_applied_successfully', [
                'order_id' => $orderData->id,
                'route_parameters' => $routeParameters
            ]);

            return redirect()->route('success-upgrade', $routeParameters);

        } catch (\Exception $e) {
            $this->logPaymentEvent($traceId, 'upgrade_processing_failed', [
                'payment_intent' => $transactionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verify the Stripe payment.
     *
     * @param string $transactionId
     * @param string $traceId
     * @return array
     */
    private function verifyPayment($transactionId, $traceId)
    {
        try {
            $this->logPaymentEvent($traceId, 'stripe_payment_verification_start', [
                'payment_intent' => $transactionId
            ]);

            $stripe = new \Stripe\StripeClient($this->stripeSettings->secret_key);
            $payment = $stripe->paymentIntents->retrieve($transactionId, []);

            $this->logPaymentEvent($traceId, 'stripe_api_response_received', [
                'payment_intent' => $transactionId,
                'status' => $payment->status ?? 'unknown',
                'amount' => $payment->amount ?? null,
                'currency' => $payment->currency ?? null,
                'created' => $payment->created ?? null
            ]);

            if ($payment && $payment->status === 'succeeded') {
                $this->logPaymentEvent($traceId, 'stripe_payment_verification_success', [
                    'payment_intent' => $transactionId,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency
                ]);
                return ['success' => true, 'response' => $payment];
            }

            $this->logPaymentEvent($traceId, 'stripe_payment_verification_failed', [
                'payment_intent' => $transactionId,
                'status' => $payment->status ?? 'unknown',
                'reason' => 'Payment status not succeeded'
            ]);

            return ['success' => false, 'message' => __('messages.t_error_payment_failed')];

        } catch (\Exception $e) {
            $this->logPaymentEvent($traceId, 'stripe_verification_exception', [
                'payment_intent' => $transactionId,
                'error' => $e->getMessage(),
                'error_type' => get_class($e)
            ]);

            return ['success' => false, 'message' => __('messages.t_error_payment_failed')];
        }
    }
}
