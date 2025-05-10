<?php

namespace App\Observers;

use App\Models\OrderPackage;

class OrderPackageObserver
{
    public function saved(OrderPackage $orderPackage)
    {
        $isActive = $orderPackage->status === 'completed';

        foreach ($orderPackage->packageItems as $packageItem) {
            if ($packageItem->adPromotion) {
                $packageItem->adPromotion->active = $isActive;
                $packageItem->adPromotion->save();
            }
        }
    }
}
