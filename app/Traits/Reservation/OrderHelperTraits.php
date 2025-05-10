<?php

namespace App\Traits\Reservation;

use App\Models\Reservation\Order;
use App\Models\Reservation\TemporaryOrder;
use App\Models\Ad;
use Filament\Notifications\Notification;
use App\Settings\FlutterwaveSettings;
use App\Settings\OfflinePaymentSettings;
use App\Settings\PaymentSettings;
use App\Settings\PaymongoSettings;
use App\Settings\PaypalSettings;
use App\Settings\PaystackSettings;
use App\Settings\RazorpaySettings;
use App\Settings\StripeSettings;

trait OrderHelperTraits
{
    public $paymentStatus;
    public $paymentMethod;

    public function createOrder($carts, $paymentStatus, $paymentMethod, $deliveryAddress, $transactionId, $orderType = RESERVATION_TYPE_RETAIL)
    {
        $order = true;

        $this->paymentStatus = $paymentStatus;
        $this->paymentMethod = $paymentMethod;

        $shippingAddress = $this->formatShippingAddress($deliveryAddress);

        if (is_ecommerce_active() && isECommerceEnableSeperateOrderConversion() || $orderType == RESERVATION_TYPE_POINT_VAULT) {
            $carts->each(function ($cart) use ($shippingAddress, $transactionId, $deliveryAddress, $paymentMethod, $orderType) {
                $isDiscounted = $cart->ad->isEnabledOffer() && $cart->ad->offer_price;
                $cartSubTotal = $cart->quantity * ($isDiscounted ? $cart->ad->offer_price : $cart->ad->price);
                $orderData = $this->initializeOrderData($cart->vendor_id, $shippingAddress, $transactionId, $deliveryAddress);

                $totalAmount = $cart->quantity * ($isDiscounted ? $cart->ad->offer_price : $cart->ad->price);
                $subTotalAmount = $cartSubTotal;
                $discountAmount = 0;

                //Calculate Tax
                $taxAmount = (!isEnablePointSystem() && isECommerceTaxOptionEnabled()) ? ($totalAmount * getECommerceTaxRate()) / 100 : 0;

                //Calculate converted total
                $convertedTotal = $this->calculateConvertedTotal($totalAmount + $taxAmount, $paymentMethod);


                $order = Order::create([
                    ...$orderData,
                    'total_amount' => $totalAmount+$taxAmount,
                    'discount_amount' => $discountAmount,
                    'subtotal_amount' => $totalAmount - $discountAmount,
                    'tax_amount' => $taxAmount,
                    'exchange_rate' => $this->getPaymentGatewayRate($paymentMethod), // Storing exchange rate
                    'converted_amount' => $convertedTotal,
                    'order_type' => $orderType,
                    'points' => $orderType == RESERVATION_TYPE_POINT_VAULT ? $subTotalAmount : 0
                ]);

                $order->items()->create([
                    'user_id' => $order->user_id,
                    'vendor_id' => $order->vendor_id,
                    'ad_id' => $cart->ad_id,
                    'quantity' => $cart->quantity,
                    'points' => $orderType == RESERVATION_TYPE_POINT_VAULT ? $cart->ad->price : 0,
                    'price' => $isDiscounted ? $cart->ad->offer_price : $cart->ad->price,
                    'discount_price' => $isDiscounted ? $cart->ad->offer_price : 0,
                    'total_price' => $cartSubTotal,
                ]);

                self::handleHistoryAndEarnings($order, $paymentMethod, $orderType);

                $cart->delete();
            });
        } else {

            $adIds = $carts->pluck('ad_id')->toArray();

            $adVendorIds = Ad::whereIn('id', $adIds)
                ->distinct()
                ->pluck('user_id')
                ->toArray();

            foreach ($adVendorIds as $vendorId) {
                $orderData = $this->initializeOrderData($vendorId, $shippingAddress, $transactionId, $deliveryAddress);
                $order = Order::create($orderData);

                $totals = $this->calculateGroupOrderTotals($carts, $order, $paymentMethod);

                $order->update($totals);

                self::handleHistoryAndEarnings($order, $paymentMethod);
            }

            TemporaryOrder::where('user_id', auth()->id())->each(function ($item) {
                $item->delete();
            });

            Notification::make()
                ->title(__('messages.t_order_placed'))
                ->success()
                ->send();
        }

        return $order;
    }

    /**
     * Retrieve the exchange rate for a specified payment method.
     * @param string $paymentMethod The payment method identifier (e.g., 'stripe', 'paypal').
     * @return float The exchange rate for the specified payment method.
     */
    private function getPaymentGatewayRate($paymentMethod)
    {
        return match ($paymentMethod) {
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

    /**
     * Calculate the converted total based on exchange rate.
     */
    private function calculateConvertedTotal($amount, $paymentMethod)
    {
        $paymentGatewayRate = $this->getPaymentGatewayRate($paymentMethod);
        $systemExchangeRate = app(PaymentSettings::class)->exchange_rate;

        return $amount * $paymentGatewayRate / $systemExchangeRate;
    }

    private function handleHistoryAndEarnings($order, $paymentMethod, $orderType = RESERVATION_TYPE_RETAIL)
    {
        $status = ['order_requested', 'order_accepted', 'order_shipped', 'order_received', 'order_rejected', 'order_not_received'];

        $order->histories()->createMany(
            collect($status)->map(function ($value, $index) use ($order) {
                return [
                    'user_id' => $order->user_id,
                    'vendor_id' => $order->vendor_id,
                    'action' => $value,
                    'command' => null,
                    'action_date' => $index === 0 ? now() : null,
                ];
            })->toArray()
        );

        if (!str_starts_with($paymentMethod, 'offline_') && $orderType != RESERVATION_TYPE_POINT_VAULT) {
            // $order->processingOrderCommission($order->subtotal_amount, $order->order_number, $order->vendor_id);
        }

    }

    private function formatShippingAddress($address)
    {
        return implode(', ', [
            $address->house_number,
            $address->address,
            $address->city->name,
            $address->state->name,
            $address->country->name,
            $address->postal_code,
        ]);
    }

    private function initializeOrderData($vendorId, $shippingAddress, $transactionId, $deliveryAddress)
    {
        return [
            'user_id' => auth()->id(),
            'vendor_id' => $vendorId,
            'order_number' => 'OR-' . uid(10) . "-" . (Order::count() + 1),
            'order_date' => now(),
            'total_amount' => 0,
            'discount_amount' => 0,
            'subtotal_amount' => 0,
            'payment_method' => str_replace('offline_', '', $this->paymentMethod),
            'order_status' => 'draft',
            'shipping_tracking_number' => null,
            'shipping_carrier' => null,
            'shipping_address' => $shippingAddress,
            'payment_status' => str_starts_with($this->paymentMethod, 'offline_') ? 'pending' : $this->paymentStatus,
            'transaction_id' => $transactionId,
            'contact_name' => $deliveryAddress->name,
            'contact_phone_number' => $deliveryAddress->phone_number,
        ];
    }

    private function calculateGroupOrderTotals($carts, $order, $paymentMethod)
    {
        $totalAmount = $discountAmount = $subTotalAmount = 0;
        $taxAmount = 0;

        $carts->where('vendor_id', $order->vendor_id)
            ->each(function ($cart) use (&$totalAmount, &$discountAmount, &$subTotalAmount, $order,&$taxAmount) {
                $isDiscounted = $cart->ad->isEnabledOffer() && $cart->ad->offer_price;
                $cartSubTotal = $cart->quantity * ($isDiscounted ? $cart->ad->offer_price : $cart->ad->price);

                $order->items()->create([
                    'user_id' => $order->user_id,
                    'vendor_id' => $order->vendor_id,
                    'ad_id' => $cart->ad_id,
                    'quantity' => $cart->quantity,
                    'price' => $isDiscounted ? $cart->ad->offer_price : $cart->ad->price,
                    'discount_price' => $isDiscounted ? $cart->ad->offer_price : 0,
                    'total_price' => $cartSubTotal,
                ]);

                $totalAmount += $cart->quantity * ($isDiscounted ? $cart->ad->offer_price : $cart->ad->price);
                $subTotalAmount += $cartSubTotal;
                $discountAmount += 0;

                // **Calculate tax for this cart item**
                if (!isEnablePointSystem() && isECommerceTaxOptionEnabled() && is_ecommerce_active()) {
                    $taxAmount += ($cartSubTotal * getECommerceTaxRate()) / 100;
                }

                $cart->delete();
            });

        $convertedAmount = $this->calculateConvertedTotal($totalAmount + $taxAmount, $paymentMethod);
        $exchangeRate = $this->getPaymentGatewayRate($paymentMethod);


        return [
            'total_amount' => $totalAmount+$taxAmount,
            'discount_amount' => $discountAmount,
            'subtotal_amount' => $totalAmount - $discountAmount,
            'tax_amount' => $taxAmount,
            'exchange_rate' => $exchangeRate, // Storing exchange rate
            'converted_amount' => $convertedAmount,
        ];
    }

}
