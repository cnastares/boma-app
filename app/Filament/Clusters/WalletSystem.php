<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Config;

class WalletSystem extends Cluster
{
    protected static ?int $navigationSort = 5;

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Wallet System Module (Addon) - Automatically manages seller earnings from purchases and withdrawal requests. Requires separate purchase.';
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
        return __('messages.t_ap_wallet_system_title');
    }
}
