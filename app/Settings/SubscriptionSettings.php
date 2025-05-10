<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SubscriptionSettings extends Settings
{
    public bool $status;
    public string $stripe_webhook_secret;
    public int $free_ad_limit;
    public bool $combine_subscriptions_and_orders;
    public bool $enable_paypal;
    public float $ad_price;
    public float $featured_ad_price;
    public float $urgent_ad_price;
    public float $spotlight_ad_price;
    public float $website_url_price;
    public float $max_adjustable_count;
    public string $ipinfo_key;
    public bool $show_comparison_table;

    public static function group(): string
    {
        return 'subscription';
    }
}
