<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PayuSettings extends Settings
{
    public ?string $name;
    public bool $status;
    public float $exchange_rate;

    public static function group(): string
    {
        return 'payu';
    }
}
