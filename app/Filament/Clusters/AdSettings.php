<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class AdSettings extends Cluster
{
    protected static ?int $navigationSort = 2;
    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }
    public function getTitle(): string
    {
        return __('messages.t_ap_ad_settings');
    }
}
