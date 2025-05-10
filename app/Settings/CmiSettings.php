<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CmiSettings extends Settings
{

    public ?string $name;
    public bool $status;
    public ?string $currency;
    public ?string $url;
    public ?string $client_id;
    public ?string $store_key;
    public float $exchange_rate;
    public static function group(): string
    {
        return 'cmi';
    }
}
