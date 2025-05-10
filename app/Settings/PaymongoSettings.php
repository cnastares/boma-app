<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymongoSettings extends Settings
{

    public ?string $name;
    public bool $status;
    public ?string $logo;
    public ?string $currency;
    public ?string $public_key;
    public ?string $secret_key;
    public ?string $authorization_token;
    public float $exchange_rate;
    public static function group(): string
    {
        return 'paymongo';
    }
}
