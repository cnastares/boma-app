<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LocalizationSettings extends Settings
{
    public string $default_language;
    public string $timezone;
    public string $date_format;

    public static function group(): string
    {
        return 'localization';
    }
}
