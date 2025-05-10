<?php

namespace App\Filament\Widgets;

use App\Models\User; // Use the correct namespace for your User model
use App\Models\Ad; // Use the correct namespace for your Ad model
use App\Models\AdPromotion;
use App\Models\Conversation; // Use the correct namespace for your Conversation model
use App\Models\Country;
use App\Models\OrderPackage;
use App\Models\OrderUpgrade;
use App\Settings\PackageSettings;
use App\Settings\PaymentSettings;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;


    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return userHasPermission('widget_StatsOverview');
    }

    protected function getHeading(): ?string
    {
        return __('messages.t_ap_stats_over_view');
    }
    protected function getStats(): array
    {
        // Fetch your stats from the database
        $totalUsers = User::count();
        $totalAds = Ad::count();
        $activeAds = Ad::where('status', 'active')->count();
        $soldAds = Ad::where('status', 'sold')->count();
        $pendingAds = Ad::where('status', 'pending')->count();
        $featuredAds = AdPromotion::where('promotion_id', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->distinct('ad_id') // Ensure unique ads are counted
            ->count();
        $totalCountries = Country::count();
        $adUpgradeRevenue = app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status ? OrderPackage::where('status', 'completed')->sum('total_value') : OrderUpgrade::where('status', 'completed')->sum('total_value');
        $userEngagement = Conversation::count(); // Replace with your logic for calculating user engagement

        // Revenue
        // Check if the package plugin is enabled and the status is active
        $packageEnabled = app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status;



        //   Counts the number of users who either do not have a verification record or have a verification record with a status of 'pending'

        $unverifiedUsersCount = User::whereDoesntHave('verification')
            ->orWhereHas('verification', function ($query) {
                $query->where('status', 'pending');
            })
            ->count();


        // Set title and description based on the package status
        $revenueTitle = $packageEnabled ? __('messages.t_ap_packages_revenue') : __('messages.t_ap_ad_upgrade_revenue');
        $revenueDescription = $packageEnabled ? __('messages.t_ap_revenue_from_packages') : __('messages.t_ap_revenue_from_ad_upgrades');

        // Get the formatted revenue value
        $formattedRevenue = formatPriceWithCurrency($adUpgradeRevenue);

        return [
            Stat::make(__('messages.t_ap_total_registered_users'), $totalUsers)
                ->description(__('messages.t_ap_user_base_size'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make(__('messages.t_ap_total_ads'), $totalAds)
                ->description(__('messages.t_ap_total_number_of_ads'))
                ->descriptionIcon('heroicon-o-list-bullet')
                ->color('success'),

            Stat::make(__('messages.t_ap_pending_ads'), $pendingAds)
                ->description(__('messages.t_ap_ads_awaiting_approval'))
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),

            Stat::make(__('messages.t_ap_active_ads'), $activeAds)
                ->description(__('messages.t_ap_currently_active_ads'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(__('messages.t_ap_sold_ads'), $soldAds)
                ->description(__('messages.t_ap_ads_marked_as_sold'))
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('info'),

            Stat::make(__('messages.t_ap_featured_ads'), $featuredAds)
                ->description(__('messages.t_ap_promoted_or_highlighted_ads'))
                ->descriptionIcon('heroicon-o-star')
                ->color('warning'),

            Stat::make(__('messages.t_ap_total_countries'), $totalCountries)
                ->description(__('messages.t_ap_number_of_countries_represented'))
                ->descriptionIcon('heroicon-o-globe-alt')
                ->color('gray'),

            Stat::make($revenueTitle, $formattedRevenue)
                ->description($revenueDescription)
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make(__('messages.t_ap_user_engagement'), $userEngagement)
                ->description(__('messages.t_ap_user_messages_or_interactions'))
                ->descriptionIcon('heroicon-o-chat-bubble-bottom-center-text')
                ->color('purple'),

            // Stat::make(__('messages.t_ap_unverify_users'), $unverifiedUsersCount)
            //     ->description(__('messages.t_ap_unverified_users'))
            //     ->descriptionIcon('heroicon-m-user-group')
            //     ->color('danger'),
        ];
    }
}
