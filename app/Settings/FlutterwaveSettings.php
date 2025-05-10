<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FlutterwaveSettings extends Settings
{
    public ?string $name;
    public bool $status;
    public ?string $logo;
    public ?string $currency;
    public ?string $public_key;
    public ?string $secret_key;
    public float $exchange_rate;
    public ?string $encryption_key;
    public ?string $environment;

    public static function group(): string
    {
        return 'flutterwave';
    }
}
