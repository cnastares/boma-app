<?php

namespace App\Traits;

use App\Models\{Ad, Promotion, AdPromotion, OrderUpgrade, OrderPromotion};
use Carbon\Carbon;

trait HandlesPromotionApplication
{
    /**
     * Apply promotions to an ad and return redirection parameters.
     */
    protected function applyPromotionsWithRedirect($orderData, $offline = false)
    {
        $this->applyPromotions($orderData, $offline);

        return $this->preparePromotionRedirectionParameters($orderData, $offline);
    }

    /**
     * Apply promotions to an ad.
     */
    protected function applyPromotions($orderData, $offline)
    {
        // Decode the JSON string into an array
        $decodedData = $offline ? $orderData : json_decode($orderData->data, true);

        // Now you can access the data
        $promotionIds = $decodedData['promotionIds'];

        $paymentMethod = $decodedData['payment_method'] ?? $orderData->payment_method;

        $userId = $decodedData['user_id'];

        $adId = $decodedData['ad_id'];

        $total = $decodedData['total'];

        $subtotal = $decodedData['subtotal'];

        $tax = $decodedData['tax'];
        $promotions = Promotion::whereIn('id', $promotionIds)->get();
        if (isset($decodedData['selected_ads']) && count($decodedData['selected_ads'])) {
            foreach ($decodedData['selected_ads'] as $ad) {
                $ad = Ad::whereSlug($ad)->first();
                if ($ad) {
                    $orderUpgrade = OrderUpgrade::create([
                        'total_value' => $total, // calculate total value
                        'subtotal_value' => $subtotal, // calculate subtotal value
                        'taxes_value' => $tax, // calculate taxes value
                        'user_id' => $userId,
                        'status' => $offline ? 'pending' : 'completed',
                        'payment_method' => $paymentMethod
                    ]);

                    foreach ($promotions as $promotion) {
                        $this->applyPromotion($promotion, $ad->id, $orderUpgrade);
                    }
                }
            }
        } else {
            $orderUpgrade = OrderUpgrade::create([
                'total_value' => $total, // calculate total value
                'subtotal_value' => $subtotal, // calculate subtotal value
                'taxes_value' => $tax, // calculate taxes value
                'user_id' => $userId,
                'status' => $offline ? 'pending' : 'completed',
                'payment_method' => $paymentMethod
            ]);

            foreach ($promotions as $promotion) {
                $this->applyPromotion($promotion, $adId, $orderUpgrade);
            }
        }

    }

    /**
     * Apply a single promotion and create associated records.
     *
     * @param Promotion $promotion
     * @param int $adId
     * @param OrderUpgrade $orderUpgrade
     */
    protected function applyPromotion(Promotion $promotion, $adId, $orderUpgrade)
    {
        $existingPromotion = AdPromotion::where('ad_id', $adId)
            ->where('promotion_id', $promotion->id)
            ->first();

        if (!$existingPromotion || $existingPromotion->end_date->isPast()) {
            $adPromotion = AdPromotion::create([
                'ad_id' => $adId,
                'promotion_id' => $promotion->id,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays($promotion->duration),
                'price' => $promotion->price,
            ]);

            OrderPromotion::create([
                'order_upgrade_id' => $orderUpgrade->id,
                'ad_promotion_id' => $adPromotion->id,
            ]);

            // Update totals in OrderUpgrade
            $orderUpgrade->update([
                'total_value' => $orderUpgrade->total_value,
                'subtotal_value' => $orderUpgrade->subtotal_value,
            ]);
        }
    }

    /**
     * Prepare redirection parameters after applying promotions.
     */
    private function preparePromotionRedirectionParameters($orderData, $offline)
    {
        $decodedData = $offline ? $orderData : json_decode($orderData->data, true);
        $adId = $decodedData['ad_id'];

        if (!$offline) {
            $orderData->delete();
        }

        return ['ad_id' => $adId];
    }
}
