<?php

namespace App\Observers;

use App\Models\OrderUpgrade;

class OrderUpgradeObserver
{
    public function saved(OrderUpgrade $orderUpgrade)
    {
        $isActive = $orderUpgrade->status === 'completed';

        foreach ($orderUpgrade->orderPromotions as $orderPromotion) {
            if ($orderPromotion->adPromotion) {
                $adPromotion = $orderPromotion->adPromotion;
                $adPromotion->active = $isActive;
                $adPromotion->save();
            }
        }
    }
}
