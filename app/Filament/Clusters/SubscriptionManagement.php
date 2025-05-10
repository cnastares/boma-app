<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Config;

// Subscription Management Cluster
class SubscriptionManagement extends Cluster
{
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Subscription Management Module (Addon) - Enable recurring membership plans for sellers with automated billing. Requires separate purchase.';
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
}
