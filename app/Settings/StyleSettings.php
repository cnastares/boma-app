<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class StyleSettings extends Settings
{

    public string $custom_style;

    public static function group(): string
    {
        return 'style';
    }
}
