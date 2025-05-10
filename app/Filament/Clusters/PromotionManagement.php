<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class PromotionManagement extends Cluster
{
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_monetization');
    }


    public function getTitle(): string
    {
        return __('messages.t_ap_promotion_management_title');
    }
}
