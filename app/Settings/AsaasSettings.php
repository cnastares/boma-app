<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AsaasSettings extends Settings
{
    public ?string $name;
    public bool $status;
    public ?string $logo;
    public ?string $currency;
    public ?string $api_key;
    public ?string $access_token;
    public float $exchange_rate;
    public ?string $environment;

    public static function group(): string
    {
        return 'asaas';
    }
}
