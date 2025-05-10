<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class AdPlacements extends Cluster
{
    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }
    public function getTitle(): string
    {
        return __('messages.t_ap_ad_placements');
    }
}
