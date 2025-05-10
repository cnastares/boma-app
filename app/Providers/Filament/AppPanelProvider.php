<?php

namespace App\Providers\Filament;

use App\Settings\AppearanceSettings;
use App\Settings\LiveChatSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Profile')->url('/my-profile'),
                'forBuyers' => MenuItem::make()->label(__('messages.t_for_buyers'))
                    ->icon(function(){
                        try{
                            return getSettingMediaUrl('appearance.switch_to_buyer_icon', 'switch_to_buyer_icon', 'heroicon-o-shopping-cart');
                        }catch(\Exception $e){
                            return 'heroicon-o-shopping-cart';
                        }
                    })
                ->url('/'),
                'logout' => MenuItem::make()->label(__('messages.t_logout'))->icon('logout-1'),
            ])
            ->bootUsing(function (Panel $panel) {
                try {
                    $faviconPath = getSettingMediaUrl('general.favicon_path', 'favicon', asset('images/favicon.png'));
                    $panel->favicon($faviconPath);
                } catch (\Exception $e) {
                    $panel->favicon(asset('images/favicon.png'));
                }
            })
            // ->brandLogo(fn() => view('components.brand'))
            ->id('app')
            ->path('dashboard')
            ->colors([
                'primary' => getPrimaryColorShades(),
            ])
            ->viteTheme('resources/css/filament/app/theme.css')
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ->navigationGroups([
                NavigationGroup::make()
                ->label(__('messages.t_ads_navigation'))
                ->icon('signage'),
                NavigationGroup::make()
                    ->label(__('messages.t_engagements_navigation'))
                    ->icon('heroicon-o-sparkles'),
                NavigationGroup::make()
                    ->label(__('messages.t_insights_navigation'))
                    ->icon('heroicon-o-user'),
            ])
            ->navigationItems([
                ...$this->getLiveChatNavigationItem(),
                    NavigationItem::make()
                    ->label(function () {
                        return __('messages.t_store_page_analytics');
                    })
                    ->group(__('messages.t_insights_navigation'))
                    ->visible(getSubscriptionSetting('status') && getActiveSubscriptionPlan())
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.app.pages.store-traffic-analytics.{id}'))
                    ->url(fn()=>route('filament.app.pages.store-traffic-analytics.{id}',['id'=>auth()->id()]))
                    ->sort(7),
            ])
            ->font($this->getFont())
            ->renderHook(
                name: 'panels::user-menu.before',
                hook: fn(): View => view('components.filament.app.header'),
            )
            ->renderHook(
                name: 'panels::body.start',
                hook: fn(): View => view('components.skip-links',[
                    'links'=>[
                        'main-content'=> __('messages.t_skip_to_main_content'),
                        'sidebar-nav'=> __('messages.t_skip_to_sidebar'),
                    ]
                ]),
            )
            // ->databaseNotifications()

            ->renderHook(
                name: 'panels::user-menu.after',
                hook: fn(): string => Blade::render("<a href='/post-ad'
                        class='bg-gray-900 post-ad block text-white py-2 px-4 rounded-xl dark:bg-primary-600 dark:text-black whitespace-nowrap'>
                        " . __('messages.t_post_your_ad') . "
                    </a>")
            )
            ->breadcrumbs(false)
        ;
    }

    protected function getLiveChatNavigationItem(): array
    {
        if (
            app('filament')->hasPlugin('live-chat')
            && Schema::hasTable('settings')
            && app(LiveChatSettings::class)->enable_livechat
        ) {

            return [
                NavigationItem::make()
                    ->label(__('messages.t_my_messages'))
                    ->group(__('messages.t_insights_navigation'))
                    ->url('/messages')
                    ->sort(7),
            ];
        }

        return [];
    }

    private function getFont()
    {
        try {
            return app(AppearanceSettings::class)?->font ?? 'DM Sans';
        } catch (\Exception $e) {
            return 'DM Sans';
        }
    }
}
