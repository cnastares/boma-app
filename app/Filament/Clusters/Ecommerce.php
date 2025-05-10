<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Config;

// Main Listing Management Cluster
class Ecommerce extends Cluster
{
    protected static ?int $navigationSort = 4;

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'E-commerce Module (Addon) - Enhance your marketplace with online shopping. Requires separate purchase.';
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
        return __('messages.t_ap_core_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_ecommerce');
    }
}
