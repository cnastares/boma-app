<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSettings extends Settings
{

    public string $currency;
    public ?float $tax_rate = null;
    public float $exchange_rate = 1.0;
    public bool $enable_tax = false;
    public string $tax_type = 'percentage';
    public string $currency_locale = 'en';
    public bool $display_currency_after_price = false;

    public static function group(): string
    {
        return 'payment';
    }
}
