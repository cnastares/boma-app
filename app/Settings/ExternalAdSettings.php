<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ExternalAdSettings extends Settings
{

    public bool $enable;
    public string $value;
    public int $ad_top_spacing;
    public int $ad_right_spacing;
    public int $ad_bottom_spacing;
    public int $ad_left_spacing;
    public static function group(): string
    {
        return 'external-ad';
    }
}
