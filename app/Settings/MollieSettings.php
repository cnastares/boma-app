<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MollieSettings extends Settings
{

    public ?string $name;
    public bool $status;
    public ?string $currency;
    public ?string $api_key;
    public float $exchange_rate;
    public static function group(): string
    {
        return 'mollie';
    }
}
