<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Config;

class DynamicFields extends Cluster
{
    protected static ?int $navigationSort = 4;

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Dynamic Fields Module (Addon) - Customize and add additional fields for each category to collect specific information. Requires separate purchase.';
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

    public function getTitle(): string
    {
        return __('messages.t_ap_dynamic_fields');
    }
}
