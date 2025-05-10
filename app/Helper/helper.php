<?php

use App\Models\Ad;
use App\Models\AdType;
use App\Models\Conversation;
use App\Settings\FlutterwaveSettings;
use App\Settings\GeneralSettings;
use App\Settings\OfflinePaymentSettings;
use App\Settings\PaymongoSettings;
use App\Settings\PaypalSettings;
use App\Settings\PaystackSettings;
use App\Settings\RazorpaySettings;
use App\Settings\StripeSettings;
use App\Settings\ThemeSettings;
use Illuminate\Support\Facades\Cache;
use App\Models\Language;
use App\Models\Promotion;
use App\Models\SettingsProperty;
use App\Settings\AppearanceSettings;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms;
use Illuminate\Support\Number;

/**
 * Fetch the active languages from the database.
 *
 * This function retrieves languages that are marked as visible from the database.
 * To improve performance, the results are cached indefinitely unless the cache
 * is explicitly cleared.
 *
 * @param bool $clear_cache If set to true, the cache for active languages will be cleared.
 * @return \Illuminate\Support\Collection A collection of active languages.
 */
function fetch_active_languages($clear_cache = false)
{
    // Define the cache key used to store the active languages.
    $cache_key = 'active_languages';

    // If clear_cache is true, remove the cached active languages.
    if ($clear_cache) {
        Cache::forget($cache_key);
    }

    // Use the cache system to store/retrieve the active languages. If the languages aren't
    // already cached, fetch them from the database and cache the result indefinitely.
    return Cache::rememberForever($cache_key, function () {
        return Language::where('is_visible', true)->orderBy('title')->get();
    });
}

/**
 * Check if the setup is complete based on the existence of the setup route file.
 *
 * @return bool
 */
function isSetupComplete(): bool
{
    $path = storage_path('installed');
    return File::exists($path);
}

/**
 * Generate a unique uppercase string.
 *
 * @param int $length The desired length of the generated string.
 * @return string The generated unique string.
 */
function uid($length = 20): string
{
    // Generate random bytes
    $bytes = random_bytes($length);

    // Convert bytes to hexadecimal
    $random = bin2hex($bytes);

    // Return the string in uppercase and trimmed to the desired length
    return strtoupper(substr($random, 0, $length));
}

/**
 * Fetches the media URL for a given setting key and media collection name.
 * If the media doesn't exist, it returns a default URL.
 *
 * @param string $settingKey The key for the setting (e.g., 'general.logo_path').
 * @param string $mediaCollectionName The name of the media collection (e.g., 'logo').
 * @param string $defaultUrl The default URL to return if the media doesn't exist.
 * @return string The media URL or the default URL.
 */
function getSettingMediaUrl(string $settingKey, string $mediaCollectionName, string $defaultPathOrUrl, bool $preferLocalPath = false): string
{
    $settingsProperty = SettingsProperty::getInstance($settingKey);

    if ($settingsProperty) {
        $media = $settingsProperty->getFirstMedia($mediaCollectionName);

        if ($media) {
            // If preferring local path and it exists, return it
            if ($preferLocalPath) {
                $localPath = $media->getPath();
                if (file_exists($localPath)) {
                    return $localPath;
                }
            }
            // Otherwise, return the URL
            return $media->getUrl();
        }
    }
    // Fallback if no media or the local file doesn't exist
    return $defaultPathOrUrl;
}


/**
 * Convert to number
 *
 * @param mixed $value
 * @return mixed
 */
function convertToNumber($value)
{
    if (is_numeric($value)) {
        $int = (int) $value;
        $float = (float) $value;

        $value = ($int == $float) ? $int : $float;
        return $value;
    } else {
        return $value;
    }
}


/**
 * Check if an addon is installed.
 *
 * @param string $moduleName The name of the module.
 * @return bool
 */
function isAddonInstalled($moduleName)
{
    $installedFilePath = base_path('app-modules/' . $moduleName . '/installed');

    return File::exists($installedFilePath);
}

/**
 * Updates or adds a key-value pair in the .env file. If the key already exists,
 * its value is updated; if not, the key-value pair is added to the end of the file.
 *
 * This function handles proper formatting of the .env file, ensuring the values
 * are correctly escaped and placed. It takes into account the different line
 * endings between Windows and other operating systems.
 *
 * @param string $envKey The environment variable key to be set or updated.
 * @param string $envValue The value to be assigned to the environment variable.
 * @return bool Returns true if the operation was successful, false otherwise.
 * @throws \Exception if there is an error during file handling.
 */
function setEnvironmentValue($envKey, $envValue)
{
    try {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$envKey}=");
        if ($keyPosition) {
            if (PHP_OS_FAMILY === 'Windows') {
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
            } else {
                $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
            }
            $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
            $envValue = str_replace(chr(92), "\\\\", $envValue);
            $envValue = str_replace('"', '\"', $envValue);
            $newLine = "{$envKey}=\"{$envValue}\"";
            if ($oldLine != $newLine) {
                $str = str_replace($oldLine, $newLine, $str);
                $str = substr($str, 0, -1);
                $fp = fopen($envFile, 'w');
                fwrite($fp, $str);
                fclose($fp);
            }
        } elseif (strtoupper($envKey) == $envKey) {
            $envValue = str_replace(chr(92), "\\\\", $envValue);
            $envValue = str_replace('"', '\"', $envValue);
            $newLine = "{$envKey}=\"{$envValue}\"\n";
            $str .= $newLine;
            $str = substr($str, 0, -1);
            $fp = fopen($envFile, 'w');
            fwrite($fp, $str);
            fclose($fp);
        }
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function updateEnvironmentValue(string $key, string $value): void
{
    $envPath = base_path('.env');
    $oldValue = env($key);

    if (file_exists($envPath)) {
        file_put_contents(
            $envPath,
            preg_replace(
                "/^{$key}=.*$/m",
                "{$key}={$value}",
                file_get_contents($envPath)
            )
        );
    }

    // Ensure the new value is loaded into the app configuration
    config([$key => $value]);
}

if (!function_exists('getCustomerCurrentBuildVersion')) {
    function getCustomerCurrentBuildVersion()
    {
        return config('app.build_version', 1);
    }
}

/**
 * Show success notification.
 *
 * @param string $message The success message to be displayed.
 * @return void
 */
function notifySuccess(string $message)
{
    Notification::make()
        ->title($message)
        ->success()
        ->send();
}

/**
 * Show error notification.
 *
 * @param string $message The error message to be displayed.
 * @return void
 */
function notifyError(string $message)
{
    Notification::make()
        ->title($message)
        ->danger()
        ->send();
}

/**
 * Convert a given string to a URL-friendly slug.
 *
 * This function takes a string and converts it into a slug by
 * replacing spaces with hyphens and converting it to lowercase.
 *
 * @param string $string The string to be slugified.
 * @return string The slugified version of the string.
 */
function slugify($string)
{
    // Replace spaces and other special characters with hyphens
    $slug = preg_replace('/[^\w]+/', '-', $string);

    // Convert to lowercase
    $slug = strtolower($slug);

    // Trim hyphens from the beginning and end of the slug
    $slug = trim($slug, '-');

    return $slug;
}


/**
 * Generate a URL for the ad-category or location-category route with optional query string.
 *
 * @param  \App\Models\Category  $category
 * @param  \App\Models\Subcategory|null  $subcategory
 * @param  string|null  $location
 * @param  string|null  $queryString
 * @return string
 */
function generate_category_url($adType, $category, $subcategory = null, $location = null, $childCategory = null)
{
    $routeParameters = [
        'category' => $category?->slug,
        'subCategory' => optional($subcategory)->slug,
        'childCategory' => optional($childCategory)->slug
    ];

    if ($location) {
        $routeParameters['location'] = $location;

        return route('location-category', $routeParameters);
    }

    if (hasMultipleAdTypes() && $adType?->slug) {
        return route('ad-type.collection', array_merge(['type' => $adType?->slug], $routeParameters));
    }

    return route('categories.collection', $routeParameters);
}

function hasMultipleAdTypes()
{
    return AdType::count() > 1;
}

/**
 * Clean text
 *
 * @param string $text
 * @return string
 */
function clean($text)
{
    try {

        return \Purify::clean($text);
    } catch (\Throwable $th) {
        return $text;
    }
}


/**
 * Format date
 *
 * @param string $timestamp
 * @param string $format
 * @return string
 */
function format_date($timestamp, $format = 'ago')
{
    // Set locale
    Carbon::setLocale(config()->get('backend_timing_locale'));

    // Check format type is ago
    if ($format === 'ago') {

        return Carbon::createFromTimeStamp(strtotime($timestamp), config('app.timezone'))->diffForHumans();
    } else {

        return Carbon::create($timestamp)->setTimezone(config('app.timezone'))->translatedFormat($format);
    }
}

/**
 * Make file size readable
 *
 * @param integer $size
 * @param integer $precision
 * @return string
 */
function format_bytes($size, $precision = 2)
{
    if ($size > 0) {
        $size = (int) $size;
        $base = log($size) / log(1024);
        $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    } else {
        return $size;
    }
}


function hexToRgb($hex, $alpha = 1)
{
    $hex = str_replace('#', '', $hex);
    $length = strlen($hex);
    $rgb = array_map(function ($part) {
        return hexdec(str_repeat($part, 2 / strlen($part)));
    }, str_split($length == 3 ? $hex : substr($hex, 0, 6), $length / 3));

    return implode(', ', $rgb) . ($alpha < 1 ? ", $alpha" : '');
}

function adjustBrightness($rgb, $percent)
{
    $rgb = array_map(function ($color) use ($percent) {
        $color += round($percent * 255 / 100);
        return max(0, min(255, $color));
    }, explode(', ', $rgb));

    return implode(', ', $rgb);
}


if (!function_exists('keyValueExist')) {
    function keyValueExist($array, $key, $value): bool
    {
        return isset($array[$key]) && in_array(json_encode($value), $array[$key]);
    }
}

if (!function_exists('getLocale')) {
    function getLocale()
    {
        if (Schema::hasTable('languages')) {
            $locale = session()->get('locale') ??
                request()->cookie('locale') ??
                app(GeneralSettings::class)->default_language ??
                config('app.locale', 'en');
            return $locale;
        }
    }
}

if (!function_exists('pluralize')) {
    function pluralize(int $count, $singular, $plural, $displayCountWhenZero = false)
    {
        if (!$count) {
            return $displayCountWhenZero ? $count . ' ' . $singular : $count;
        } else {
            if ($count == 1) {
                return $count . ' ' . $singular;
            } else {
                return $count . ' ' . $plural;
            }
        }
    }
}

if (!function_exists('getPaymentLabel')) {
    function getPaymentLabel($paymentMethod)
    {
        if ($paymentMethod == 'stripe' && app(StripeSettings::class)->status) {
            return app(StripeSettings::class)->name;
        }

        if ($paymentMethod == 'paypal' && app('filament')->hasPlugin('paypal')) {
            return app(PaypalSettings::class)->name;
        }

        if ($paymentMethod == 'flutterwave' && app('filament')->hasPlugin('flutterwave')) {
            return app(FlutterwaveSettings::class)->name;
        }

        if ($paymentMethod == 'offline' && app('filament')->hasPlugin('offline-payment')) {
            return app(OfflinePaymentSettings::class)->name;
        }
        if ($paymentMethod == 'paymongo' && app('filament')->hasPlugin('paymongo')) {
            return app(PaymongoSettings::class)->name;
        }

        if ($paymentMethod == 'razorpay' && app('filament')->hasPlugin('razorpay')) {
            return app(RazorpaySettings::class)->name;
        }
        if ($paymentMethod == 'paystack' && app('filament')->hasPlugin('paystack')) {
            return app(PaystackSettings::class)->name;
        }

        return $paymentMethod;
    }
}

function current_marketplace()
{
    return env('MARKETPLACE') ?? 'classified';
}

function is_classified_active()
{
    return current_marketplace() == 'classified';
}

function is_vehicle_rental_active()
{
    return current_marketplace() == 'vehicle_rental';
}

function has_plugin_vehicle_rental_marketplace()
{
    return (app('filament')->hasPlugin('vehicle-rental-marketplace'));
}

if (!function_exists('app_name')) {
    function app_name()
    {
        return config('app.name');
    }
}

if (!function_exists('getPrimaryColorShades')) {
    function getPrimaryColorShades()
    {
        try {
            $primaryColorHex = app(AppearanceSettings::class)?->primary_color ?? '#FDae4B';
        } catch (\Exception $e) {
            // If there's an error (e.g., database not available), use the default color
            $primaryColorHex = '#FDae4B';
        }

        $primaryRgb = hexToRgb($primaryColorHex);
        $darkerPrimaryRgb1 = adjustBrightness($primaryRgb, 60);
        $darkerPrimaryRgb10 = adjustBrightness($primaryRgb, -10);
        $darkerPrimaryRgb20 = adjustBrightness($primaryRgb, -20);
        $darkerPrimaryRgb25 = adjustBrightness($primaryRgb, -25);

        return [
            50 => $darkerPrimaryRgb1,
            400 => $primaryRgb,
            500 => $darkerPrimaryRgb10,
            600 => $darkerPrimaryRgb20,
            700 => $darkerPrimaryRgb25,
        ];
    }
}
if (!function_exists('getClientIp')) {

    function getClientIp()
    {
        $ipaddress = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'unknown';
        }

        return $ipaddress;
    }
}
if (!function_exists('country2flag')) {
    function country2flag(string $countryCode): string
    {
        return (string) preg_replace_callback(
            '/./',
            static fn(array $letter) => mb_chr(ord($letter[0]) % 32 + 0x1F1E5),
            $countryCode
        );
    }
}

if (!function_exists('getReceiverIdFromConversation')) {
    function getReceiverIdFromConversation($conversation_id)
    {
        return Conversation::find($conversation_id)?->seller_id;
    }
}
if (!function_exists('notifySellerInteractionReached')) {
    function notifySellerInteractionReached()
    {
        Notification::make()
            ->danger()
            ->duration(60000)
            ->title(__('messages.t_seller_reached_interaction_message'))
            ->send();
    }
}
function getOfferSuggestions($initialAmount)
{
    $multipliers = [0.97, 0.95, 0.92]; // 95%, 90%, and 80% of initial price
    $offerAmount = [$initialAmount];
    $suggestions = array_map(function ($multiplier) use ($initialAmount) {
        return round($initialAmount * $multiplier, 2);
    }, $multipliers);

    return array_merge($offerAmount, $suggestions);
}

function maxUploadFileSize()
{
    return 1024;
}

if (!function_exists('userHasPermission')) {

    function userHasPermission(string $permission)
    {
        $user = Filament::auth()->user();

        // First check if user is admin
        if ($user->is_admin) {
            return true;
        }

        $userHavePermissions = Filament::auth()->user()->roles()->first()?->permissions()->pluck('name')->toArray() ?? [];

        return in_array($permission, $userHavePermissions);
    }
}

if (!function_exists('getIcon')) {

    function getIcon($key, $type)
    {
        $iconPath = '/images/';
        $icons = [
            'os' => [
                'Windows' => 'windows.png',
                'Windows NT' => 'windows_nt.png',
                'OS X' => 'macos.png',
                'Debian' => 'debian.png',
                'Ubuntu' => 'ubuntu.png',
                'Macintosh' => 'ppc.png',
                'OpenBSD' => 'openbsd.png',
                'Linux' => 'linux.png',
                'ChromeOS' => 'chromeos.png',
            ],
            'browser' => [
                'Opera Mini' => 'opera_mini.png',
                'Opera' => 'opera.png',
                'Edge' => 'edge.png',
                'Coc Coc' => 'coc_coc.png',
                'UCBrowser' => 'ucbrowser.png',
                'Vivaldi' => 'vivaldi.png',
                'Chrome' => 'chrome.png',
                'Firefox' => 'firefox.png',
                'Safari' => 'safari.png',
                'IE' => 'ie.png',
                'Netscape' => 'netscape.png',
                'Mozilla' => 'mozilla.png',
                'WeChat' => 'wechat.png',
            ],
        ];

        return isset($icons[$type][$key]) ? $iconPath . $icons[$type] . '/' . $icons[$type][$key] : null;
    }
}
if (!function_exists('getSecondaryColorShades')) {
    function getSecondaryColorShades()
    {
        try {
            $primaryColorHex = app(AppearanceSettings::class)?->secondary_color ?? '#FEBD69';
        } catch (\Exception $e) {
            // If there's an error (e.g., database not available), use the default color
            $primaryColorHex = '#FEBD69';
        }

        $primaryRgb = hexToRgb($primaryColorHex);
        $darkerPrimaryRgb1 = adjustBrightness($primaryRgb, 60);
        $darkerPrimaryRgb10 = adjustBrightness($primaryRgb, -10);
        $darkerPrimaryRgb20 = adjustBrightness($primaryRgb, -20);
        $darkerPrimaryRgb25 = adjustBrightness($primaryRgb, -25);

        return [
            50 => $darkerPrimaryRgb1,
            400 => $primaryRgb,
            500 => $darkerPrimaryRgb10,
            600 => $darkerPrimaryRgb20,
            700 => $darkerPrimaryRgb25,
        ];
    }
}
if (!function_exists('getCurrentTheme')) {
    function getCurrentTheme()
    {
        return app(ThemeSettings::class)?->selected_theme ?? 'classic';
    }
}

if (!function_exists('getDefaultAdPlaceholderImage')) {
    /**
     * Get default ad placeholder from ad settings.
     * This function returns the default ad placeholder image set in the ad settings.
     * If no image is set, it returns the default placeholder image from the asset folder.
     * @return string
     */
    function getDefaultAdPlaceholderImage()
    {
        $placeholder = getSettingMediaUrl('ad.placeholder_image', 'placeholder_images', asset('images/placeholder.jpg'));
        return $placeholder;
    }
}

if (!function_exists('getAdPlaceholderImage')) {
    /**
     * Get Ad placeholder based on main category.
     * If placeholder not available in the main category then return default placeholder image.
     *
     * @param mixed $adId
     * @return mixed
     */
    function getAdPlaceholderImage($adId)
    {
        $ad = Ad::with('category.parent')->firstWhere('id', $adId);
        $mainCategory = $ad?->category?->parent;
        $placeholderImage = $mainCategory?->getFirstMediaUrl('placeholder_images') ? $mainCategory->getFirstMediaUrl('placeholder_images') : getDefaultAdPlaceholderImage();
        return $placeholderImage;
    }
}

if (!function_exists('currencyToPointConversion')) {
    function currencyToPointConversion($value)
    {
        try {
            // Check if the point system is enabled
            if (isEnablePointSystem()) {
                $shortName = getPointSystemSetting('short_name');
                $formattedValue = str_replace(config('app.currency_symbol'), '', $value);

                return "{$formattedValue} {$shortName}";
            }

            // Return the original value if the point system is not enabled
            return $value;
        } catch (Exception $ex) {
            // Log the exception and return the original value
            \Log::error('Error in currencyToPointConversion: ' . $ex->getMessage());
            return $value;
        }
    }
}

if (!function_exists('getVerificationFields')) {
    /**
     * Retrieves an array of verification fields for user settings.
     *
     * @return array An array of form components for verification settings.
     */
    function getVerificationFields(): array
    {
        return [
            Toggle::make('enable_age_verify')
                ->label(__('messages.t_ap_enable_age_verify'))
                ->helperText(__('messages.t_ap_enable_age_verify_toggle_help'))
                ->live(),

            Forms\Components\TextInput::make('age_value')
                ->minValue(1)
                ->label(__('messages.t_ap_age_value'))
                ->numeric()
                ->required()
                ->hidden(fn($get) => !$get('enable_age_verify')),

            Toggle::make('enable_identity_verify')
                ->label(__('messages.t_ap_enable_identity_verify'))
                ->helperText(__('messages.t_ap_enable_identity_verify_toggle_help'))
                ->live(),

            Toggle::make('enable_manual_approval')
                ->label(__('messages.t_ap_enable_manual_approval'))
                ->helperText(__('messages.t_ap_enable_manual_approval_toggle_help'))
                ->live(),
        ];
    }
}

if (!function_exists('formatPriceWithCurrency')) {
    function formatPriceWithCurrency($price)
    {
        $currencySymbol = config('app.currency_symbol'); // Fetch currency symbol from config
        $locale = getPaymentSystemSetting('currency_locale') ?? 'en_US'; // Default to 'en_US' if not set

        $formattedPrice = Number::format($price, locale: $locale);

        return isDisplayCurrencyAfterPrice()
            ? "{$formattedPrice} {$currencySymbol}"
            : "{$currencySymbol} {$formattedPrice}";
    }
}
if (!function_exists('getOrderStatusColor')) {
    function getOrderStatusColor(string $state): string
    {
        return match ($state) {
            'order_request' => 'info',
            'order_accepted' => 'info',
            'order_processed' => 'warning',
            'order_shipped' => 'warning',
            'order_received' => 'success',
            'order_cancelled' => 'danger',
            'order_rejected' => 'danger',
            'order_not_received'=>'danger',
            default => 'info'
        };
    }
}
if (!function_exists('isFeaturedAdEnabled')) {
    function isFeaturedEnabled(): bool
    {
        $promotion = Promotion::find(1);
        return $promotion && $promotion->is_enabled;
    }
}

if (!function_exists('isSpotlightAdEnabled')) {
    function isSpotlightEnabled(): bool
    {
        $promotion = Promotion::find(2);
        return $promotion && $promotion->is_enabled;
    }
}

if (!function_exists('isUrgentAdEnabled')) {
    function isUrgentAdEnabled(): bool
    {
        $promotion = Promotion::find(3);
        return $promotion && $promotion->is_enabled;
    }
}

if (!function_exists('isWebsiteUrlEnabled')) {
    function isWebsiteUrlEnabled(): bool
    {
        $promotion = Promotion::find(4);
        return $promotion && $promotion->is_enabled;
    }
}
