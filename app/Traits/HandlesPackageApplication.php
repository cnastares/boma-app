<?php

namespace App\Traits;

use App\Models\{AdPromotion, OrderPackage, PackageItem, OrderPackageItem, UsedPackageItem};
use Carbon\Carbon;

trait HandlesPackageApplication
{
    protected function applyPackages($orderData, $offline = false)
    {
        // Conditional assignment based on whether the payment is offline
        $decodedData = $offline ? $orderData : json_decode($orderData->data, true);

        $packageItemIds = $decodedData['packageItemIds'];

        $paymentMethod = $decodedData['payment_method'] ?? $orderData->payment_method;

        $userId = $decodedData['user_id'];

        $total = $decodedData['total'];

        $subtotal = $decodedData['subtotal'];

        $tax = $decodedData['tax'];

        $orderPackage = OrderPackage::create([
            'total_value' => $total,
            'subtotal_value' => $subtotal,
            'taxes_value' => $tax,
            'user_id' => $userId,
            'status' => $offline ?  'pending' : 'completed',
            'payment_method' => $paymentMethod
        ]);

        foreach ($packageItemIds as $packageItemId) {
            $packageItem = PackageItem::find($packageItemId);

            if ($packageItem) {
                $today = Carbon::today();
                $name = $packageItem->promotion ? $packageItem->promotion->promotion->name : 'Extra Ad Postings';
                $promotion_id = $packageItem->promotion ? $packageItem->promotion->promotion->id : null;
                $duration = $packageItem->promotion ? $packageItem->promotion->package->duration : $packageItem->package->duration;
                $isOfferActive = $packageItem->offer_enabled && $packageItem->offer_price && $packageItem->offer_start <= $today && $packageItem->offer_end >= $today;
                $activationDate = Carbon::now();
                $expiryDate = $activationDate->copy()->addDays($duration);

                $orderPackageItem = OrderPackageItem::create([
                    'order_package_id' => $orderPackage->id,
                    'package_item_id' => $packageItem->id,
                    'promotion_id' => $promotion_id,
                    'name' => $name,
                    'price' => $isOfferActive ? $packageItem->offer_price :  $packageItem->price,
                    'activation_date' => $activationDate,
                    'expiry_date' => $expiryDate,
                    'purchased' => $packageItem->quantity,
                    'available' => $packageItem->quantity,
                    'duration' => $duration,
                    'type' => $packageItem->promotion ? 'promotion' : 'ad_count'
                ]);

                if (isset($decodedData['ad_id'])) {
                    if($packageItem->promotion) {
                        AdPromotion::create([
                            'ad_id' => $decodedData['ad_id'],
                            'promotion_id' => $packageItem->promotion->promotion->id,
                            'start_date' => Carbon::now(),
                            'end_date' => Carbon::now()->addDays($duration),
                            'order_package_item_id' => $orderPackageItem->id,
                            'price' => $orderPackageItem->price
                        ]);
                        $orderPackageItem->decrement('available');
                        $orderPackageItem->increment('used');
                        UsedPackageItem::create([
                            'ad_id' => $decodedData['ad_id'],
                            'order_package_item_id' =>  $orderPackageItem->id,
                        ]);

                    }
                }
            }
        }

        return $this->prepareRedirectionParameters($orderData, $offline);

    }

    private function prepareRedirectionParameters($orderData, $offline)
    {
        $decodedData = $offline ? $orderData : json_decode($orderData->data, true);

        $routeParameters = ['actionType' => $decodedData['action_type']];

        if (isset($decodedData['ad_id'])) {
            $routeParameters['ad_id'] = $decodedData['ad_id'];
        }

        if(!$offline) {
          $orderData->delete();
        }

        return $routeParameters;
    }
}
