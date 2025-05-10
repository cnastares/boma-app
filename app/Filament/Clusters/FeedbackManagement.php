<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Config;

class FeedbackManagement extends Cluster
{
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_seller_reviews');
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Seller Review Module (Addon) - Allow buyers to rate and review seller profiles based on their experience. Requires separate purchase.';
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
        return __('messages.t_ap_user_access');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_feedback_management');
    }
}
