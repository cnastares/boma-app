<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppearanceSettings extends Settings
{
    public string $primary_color;
    public string $secondary_color;
    public bool   $enable_theme_switcher;
    public string $default_theme;
    public string $home_banner_image;
    public string $font;
    public bool $enable_contrast_toggle;
    public string $contrast_mode;
    public bool $display_site_name;
    public string $switch_to_buyer_icon;

    public static function group(): string
    {
        return 'appearance';
    }
}
