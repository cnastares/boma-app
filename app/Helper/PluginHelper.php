<?php

use Adfox\ECommerce\Settings\ECommerceSetting;
use Adfox\ECommerce\Settings\WalletSystemSetting;
use App\Settings\PointVaultSettings;
use App\Settings\MapViewSettings;
use App\Settings\PaymentSettings;
use App\Settings\PaypalSettings;
use App\Settings\SubscriptionSettings;

function isWalletSystemPluginEnabled()
{
    return app('filament')->hasPlugin('wallet-system');
}

function getWalletSystemSetting($setting)
{
    if (!isWalletSystemPluginEnabled()) {
        return false; // Return false if the plugin is not enabled
    }

    try {
        return app(WalletSystemSetting::class)->$setting; // Use dynamic property access
    } catch (Exception $ex) {
        return false;
    }
}

function isWalletSystemEnabled()
{
    return getWalletSystemSetting('enable_wallet_system');
}

function isWalletSystemBuyNowEnabled()
{
    return getWalletSystemSetting('enable_pay_now');
}

function isCommissionEnabled()
{
    return getWalletSystemSetting('enable_commission');
}

function getCommissionType()
{
    return getWalletSystemSetting('commission_type');
}

function getCommissionValue()
{
    return getWalletSystemSetting('commission_value');
}

function calculateCommissionAmount($amount)
{
    $value = getCommissionValue();

    return (getCommissionType() == 'fixed') ? $value : (($value / 100) * $amount);
}

function getCurrencySetting()
{
    return app(PaymentSettings::class)->currency;
}

// eCommerce functions
function isECommercePluginEnabled()
{
    return app('filament')->hasPlugin('eCommerce');
}

function getECommerceSystemSetting($setting)
{
    if (!isECommercePluginEnabled()) {
        return false; // Return false if the plugin is not enabled
    }

    try {
        return app(ECommerceSetting::class)->$setting; // Use dynamic property access
    } catch (Exception $ex) {
        return false;
    }
}

function is_ecommerce_active()
{
    return getECommerceSystemSetting('enable_e_commerce');
}

function isECommerceBuyNowEnabled()
{
    return getECommerceSystemSetting('enable_pay_now');
}

function isECommerceAddToCardEnabled()
{
    return getECommerceSystemSetting('enable_add_to_cart');
}

function isECommerceQuantityOptionEnabled()
{
    return getECommerceSystemSetting('enable_quantity_option');
}

function isECommerceTaxOptionEnabled()
{
    return getECommerceSystemSetting('enable_tax');
}

function getECommerceTaxRate()
{
    $value = getECommerceSystemSetting('tax_rate');

    return empty($value) ? 1 : $value;
}

function isECommerceEnableSingleOrderConversion()
{
    return getECommerceSystemSetting('enable_single_order_conversion');
}

function isECommerceEnableSeperateOrderConversion()
{
    return getECommerceSystemSetting('enable_single_order_conversion');
}

function getECommerceMaximumQuantityPerItem()
{
    $value = getECommerceSystemSetting('maximum_quantity_per_item');

    return empty($value) ? 1 : $value;
}

// Paypal functions
function isPaypalPluginEnabled()
{
    return app('filament')->hasPlugin('paypal');
}

function getPaypalSetting($setting)
{
    if (!isPaypalPluginEnabled()) {
        return false; // Return false if the plugin is not enabled
    }

    try {
        return app(PaypalSettings::class)->$setting; // Use dynamic property access
    } catch (Exception $ex) {
        return false;
    }
}

function isPaypalEnabled()
{
    return getPaypalSetting('status');
}

function getPaypalExchangeRate()
{
    return getPaypalSetting('exchange_rate');
}

function getPaypalCurrency()
{
    return getPaypalSetting('currency');
}

// Subscription functions
function isSubscriptionPluginEnabled()
{
    return app('filament')->hasPlugin('subscription');
}

function getSubscriptionSetting($setting)
{
    if (!isSubscriptionPluginEnabled()) {
        return false; // Return false if the plugin is not enabled
    }

    try {
        return app(SubscriptionSettings::class)->$setting; // Use dynamic property access
    } catch (Exception $ex) {
        return false;
    }
}

function isSubscriptionStatusEnabled()
{
    return getSubscriptionSetting('status');
}

function getSubscriptionFreeAdLimit()
{
    return getSubscriptionSetting('free_ad_limit');
}

function isSubscriptionPaypalEnabled()
{
    return getSubscriptionSetting('enable_paypal');
}


function isMapViewPluginEnabled()
{
    return app('filament')->hasPlugin('map-view');
}

function getMapViewSetting($setting)
{
    if (!isMapViewPluginEnabled()) {
        return false; // Return false if the plugin is not enabled
    }

    try {
        return app(MapViewSettings::class)->$setting; // Use dynamic property access
    } catch (Exception $ex) {
        return false;
    }
}

function isMapViewEnabled()
{
    return getMapViewSetting('enable');
}

function isMapViewEnableContainerMaxWidth()
{
    return getMapViewSetting('enable_container_max_width');
}

function isMapViewShowFilterPopup()
{
    return getMapViewSetting('show_filter_popup');
}

function isShowMapInFullScreen()
{
    return getMapViewSetting('show_map_in_fullscreen');
}

function mapMarkerDisplayType()
{
    return getMapViewSetting('map_marker_display_type');
}

function isVehicleRentalMarketplacePluginEnabled()
{
    return (app('filament')->hasPlugin('vehicle-rental-marketplace'));
}

function isFieldTemplatePluginEnabled()
{
    return app('filament')->hasPlugin('field-template');
}

function isPointSystemPluginEnabled()
{
    return app('filament')->hasPlugin('point-vault');
}

function isEnablePointSystem()
{
    try {
        return isPointSystemPluginEnabled() && app(PointVaultSettings::class)->enable_point_system;
    } catch (Exception $ex) {
        return false;
    }
}

function getPointSystemSetting($setting)
{
    if (!isEnablePointSystem()) {
        return false; // Return false if the plugin is not enabled
    }

    try {
        return app(PointVaultSettings::class)->$setting; // Use dynamic property access
    } catch (Exception $ex) {
        return false;
    }
}

if (!function_exists('getPaymentSystemSetting')) {
    function getPaymentSystemSetting($setting)
    {

        try {
            return app(PaymentSettings::class)->$setting; // Use dynamic property access
        } catch (Exception $ex) {
            return false;
        }
    }
}

if (!function_exists('isDisplayCurrencyAfterPrice')) {
    function isDisplayCurrencyAfterPrice()
    {
        return getPaymentSystemSetting('display_currency_after_price');
    }
}
