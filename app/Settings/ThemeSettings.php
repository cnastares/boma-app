<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ThemeSettings extends Settings
{
    public string $selected_theme;

    public static function group(): string
    {
        return 'theme';
    }
}
