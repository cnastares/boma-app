<?php

namespace App\Http\Controllers\Callback;

use App\Http\Controllers\Controller;
use App\Traits\HandlesPackageApplication;
use App\Models\WebhookUpgrade;
use App\Models\WebhookPackage;
use Illuminate\Http\Request;
use App\Settings\StripeSettings;
use App\Traits\HandlesPromotionApplication;


class StripeController extends Controller
{
    use HandlesPackageApplication;
    use HandlesPromotionApplication;

    private $stripeSettings;

    /**
     * Handle the callback from the Stripe payment gateway.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        $this->stripeSettings = app(StripeSettings::class);

        $transactionId = $request->get('payment_intent');
        $action = $request->get('action');

        if ($transactionId && $action) {
            $response = $this->verifyPayment($transactionId);
            if ($response['success']) {
                if ($action == 'PKG') {
                    $orderData = WebhookPackage::where('payment_id', $transactionId)
                        ->where('payment_method', 'stripe')
                        ->where('status', 'pending')
                        ->firstOrFail();

                    $routeParameters = $this->applyPackages($orderData);

                    return redirect()->route('package-success', $routeParameters);
                } else {
                    $orderData = WebhookUpgrade::where('payment_id', $transactionId)
                        ->where('payment_method', 'stripe')
                        ->where('status', 'pending')
                        ->firstOrFail();

                    $routeParameters = $this->applyPromotionsWithRedirect($orderData);

                    return redirect()->route('success-upgrade', $routeParameters);
                }
            }
        }

        return redirect('/');
    }

    /**
     * Verify the Stripe payment.
     *
     * @param string $transactionId
     * @return array
     */
    private function verifyPayment($transactionId)
    {
        $stripe = new \Stripe\StripeClient($this->stripeSettings->secret_key);
        $payment = $stripe->paymentIntents->retrieve($transactionId, []);

        if ($payment && $payment->status === 'succeeded') {
            return ['success' => true, 'response' => $payment];
        }

        return ['success' => false, 'message' => __('messages.t_error_payment_failed')];
    }
}
