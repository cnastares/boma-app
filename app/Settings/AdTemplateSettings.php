<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AdTemplateSettings extends Settings
{
    public int $max_line;
    public string $theme;


    public static function group(): string
    {
        return 'ad-template';
    }
}
