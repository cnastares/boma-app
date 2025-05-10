<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PhonePeSettings extends Settings
{
    public ?string $name;
    public bool $status;
    public ?string $merchantId;
    public ?string $saltKey;
    public ?string $saltIndex;
    public float $exchange_rate;
    public ?string $env_mode;

    public static function group(): string
    {
        return 'phonepe';
    }
}
