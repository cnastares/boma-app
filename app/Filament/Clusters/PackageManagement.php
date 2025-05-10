<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Config;

class PackageManagement extends Cluster
{
    protected static ?int $navigationSort = 3;

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Package Management Module (Addon) - Create and manage listing packages with custom limits and features for sellers. Requires separate purchase.';
    }

    public static function getNavigationBadge(): ?string
    {
        $isDemo = Config::get('app.demo');
        return  $isDemo  ? 'Addon' : '';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }


    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_monetization');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_package_management_title');
    }
}
