<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PackageSettings extends Settings
{

    public bool $status;
    public string $ad_renewal_period;
    public int $free_ad_limit;

    public static function group(): string
    {
        return 'package';
    }
}
