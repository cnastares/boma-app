<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class RazorpaySettings extends Settings
{

    public ?string $name;
    public bool $status;
    public ?string $logo;
    public ?string $currency;
    public ?string $key_id;
    public ?string $key_secret;
    public float $exchange_rate;
    public static function group(): string
    {
        return 'razorpay';
    }
}
