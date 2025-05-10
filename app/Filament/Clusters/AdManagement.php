<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

// Main Listing Management Cluster
class AdManagement extends Cluster
{
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_core_management');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_ad_management');
    }
}
