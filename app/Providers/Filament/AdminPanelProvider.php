<?php

namespace App\Providers\Filament;

use App\Settings\GeneralSettings;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\Facades\Blade;
use Filament\SpatieLaravelTranslatablePlugin;
use Illuminate\Support\Facades\Schema;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Fetching active languages and mapping them to an array of language codes
        $activeLanguages = ['en'];

        try {
            if (Schema::hasTable('languages')) {
                $activeLanguages = fetch_active_languages()->map(function ($lang) {
                    return $lang->lang_code;
                })->toArray();
            }
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            // For now, we'll just use the default 'en' language
        }

        return $panel
            ->bootUsing(function (Panel $panel) {
                try {
                    $faviconPath = getSettingMediaUrl('general.favicon_path', 'favicon', asset('images/favicon.png'));
                    $panel->favicon($faviconPath);

                } catch (\Exception $e) {
                    $panel->favicon(asset('images/favicon.png'));
                }
            })
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Slate,
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->authGuard('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->font('DM Sans', provider: GoogleFontProvider::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // \BezhanSalleh\FilamentGoogleAnalytics\Widgets\PageViewsWidget::class,
                // \BezhanSalleh\FilamentGoogleAnalytics\Widgets\VisitorsWidget::class,
                // \BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersOneDayWidget::class,
                // \BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersSevenDayWidget::class,
                // \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsWidget::class,
                // \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsDurationWidget::class,
                // \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsByCountryWidget::class,
                // \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsByDeviceWidget::class,
                // \BezhanSalleh\FilamentGoogleAnalytics\Widgets\MostVisitedPagesWidget::class,
                // \BezhanSalleh\FilamentGoogleAnalytics\Widgets\TopReferrersListWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                ->label(__('messages.t_ap_core_management'))
                ->icon('heroicon-o-squares-2x2'),

                NavigationGroup::make()
                ->label(__('messages.t_ap_user_access'))
                ->icon('heroicon-o-users'),

                NavigationGroup::make()
                ->label(__('messages.t_ap_monetization'))
                ->icon('heroicon-o-currency-dollar'),

                NavigationGroup::make()
                ->label(__('messages.t_ap_content_design'))
                ->icon('heroicon-o-pencil-square'),

                NavigationGroup::make()
                ->label(__('messages.t_ap_system_config'))
                ->icon('heroicon-o-cog-6-tooth'),

                NavigationGroup::make()
                    ->label(__('messages.t_ap_payment_gateways'))
                    ->icon('heroicon-o-banknotes'),

                NavigationGroup::make()
                    ->label(__('messages.t_ap_settings'))
                    ->icon('heroicon-o-cog-6-tooth'),

                NavigationGroup::make()
                    ->label(__('messages.t_ap_system_manager'))
                    ->icon('heroicon-o-wrench-screwdriver'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                SpatieLaravelTranslatablePlugin::make()->defaultLocales($activeLanguages),
            ])
            ->passwordReset()
            ->profile()
            ->renderHook(
                name: 'panels::user-menu.before',
                hook: fn(): string => Blade::render('@livewire(\'admin.system.cache-manager\')')
            )
            ->databaseNotifications();
    }
}
