<?php
namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaypalSettings extends Settings
{
    public ?string $name;
    public bool $status;
    public ?string $mode;
    public ?string $logo;
    public ?string $currency;
    public ?string $client_id;
    public ?string $client_secret;
    public float $exchange_rate;

    public static function group(): string
    {
        return 'paypal';
    }
}

