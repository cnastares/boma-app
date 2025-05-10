<?php

namespace App\Providers;

use Adfox\WalletSystem\Settings\WalletSystemSetting;
use App\Settings\AdListSettings;
use App\Settings\AdPlacementSettings;
use App\Settings\CategoryAdSettings;
use App\Settings\MessageSettings;
use App\Settings\ThemeSettings;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use App\Settings\AdSettings;
use App\Settings\AdTemplateSettings;
use App\Settings\GeneralSettings;
use App\Settings\LocationSettings;
use App\Settings\PaymentSettings;
use App\Settings\ScriptSettings;
use App\Settings\AuthSettings;
use App\Settings\ReCaptchaConfig;
use App\Settings\SocialSettings;
use App\Settings\StyleSettings;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Illuminate\Mail\MailManager;
use App\Models\Message;
use App\Models\OrderPackage;
use App\Models\OrderUpgrade;
use App\Observers\MessageObserver;
use App\Observers\OrderPackageObserver;
use App\Observers\OrderUpgradeObserver;
use App\Settings\AppearanceSettings;
use App\Settings\BlogSettings;
use App\Settings\EmailSettings;
use App\Settings\ExternalAdSettings;
use App\Settings\FeedbackSettings;
use App\Settings\GoogleLocationKitSettings;
use App\Settings\HomeSettings;
use App\Settings\LiveChatSettings;
use App\Settings\PackageSettings;
use TimeHunter\LaravelGoogleReCaptchaV3\Interfaces\ReCaptchaConfigV3Interface;
use App\Settings\LoginOtpSettings;
use App\Settings\MapViewSettings;
use App\Settings\PhoneSettings;
use App\Settings\PwaSettings;
use App\Settings\UiCustomizationSettings;
use App\Settings\VehicleRentalSettings;
use App\Settings\WhatsappSettings;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(
            ReCaptchaConfigV3Interface::class,
            ReCaptchaConfig::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(MailManager $mailManager): void
    {
        // Fetching active languages and mapping them to an array of language codes
        $activeLanguages = [];
        try {
            if (Schema::hasTable('languages')) {
                $activeLanguages = fetch_active_languages()->map(function ($lang) {
                    return $lang->lang_code;
                })->toArray();
            } else {
                $activeLanguages = ['en']; // Default to English if the table doesn't exist
            }

            LanguageSwitch::configureUsing(function (LanguageSwitch $switch) use ($activeLanguages) {
                $switch
                    ->userPreferredLocale(head($activeLanguages) ?? 'en')
                    ->locales($activeLanguages);
            });
        } catch (\Exception $e) {
            // If there's a database error, set a default configuration
            $activeLanguages = ['en'];
            LanguageSwitch::configureUsing(function (LanguageSwitch $switch) use ($activeLanguages) {
                $switch
                    ->userPreferredLocale('en')
                    ->locales($activeLanguages);
            });
        }
        DatabaseNotifications::trigger('notifications.database-notifications-trigger');
        // Message::observe(MessageObserver::class);
        OrderUpgrade::observe(OrderUpgradeObserver::class);
        OrderPackage::observe(OrderPackageObserver::class);
        try {
            View::share('generalSettings', app(GeneralSettings::class));
            View::share('adSettings', app(AdSettings::class));
            View::share('locationSettings', app(LocationSettings::class));
            View::share('paymentSettings', app(PaymentSettings::class));
            View::share('scriptSettings', app(ScriptSettings::class));
            View::share('adPlacementSettings', app(AdPlacementSettings::class));
            View::share('authSettings', app(AuthSettings::class));
            View::share('socialSettings', app(SocialSettings::class));
            View::share('packageSettings', app(PackageSettings::class));
            View::share('blogSettings', app(BlogSettings::class));
            View::share('loginOtpSettings', app(LoginOtpSettings::class));
            View::share('liveChatSettings', app(LiveChatSettings::class));
            View::share('feedbackSettings', app(FeedbackSettings::class));
            View::share('appearanceSettings', app(AppearanceSettings::class));
            View::share('styleSettings', app(StyleSettings::class));
            View::share('pwaSettings', app(PwaSettings::class));
            View::share('homeSettings', app(HomeSettings::class));
            View::share('emailSettings', app(EmailSettings::class));
            View::share('adTemplateSettings', app(AdTemplateSettings::class));
            View::share('mapViewSettings', app(MapViewSettings::class));
            View::share('vehicleRentalSettings', app(VehicleRentalSettings::class));
            View::share('externalAdSettings', app(ExternalAdSettings::class));
            View::share('googleSettings', app(GoogleLocationKitSettings::class));
            View::share('phoneSettings', app(PhoneSettings::class));
            View::share('customizationSettings', app(UiCustomizationSettings::class));
            View::share('messageSettings', app(MessageSettings::class));
            View::share('themeSettings', app(ThemeSettings::class));
            View::share('adListSettings', app(AdListSettings::class));
            View::share('categoryAdSettings', app(CategoryAdSettings::class));

            if (isWalletSystemPluginEnabled()) {
                View::share('walletSystemSetting', app(WalletSystemSetting::class));
            }
        } catch (\Exception $e) {
            // Log the error or handle it as needed
        }
    }
}
