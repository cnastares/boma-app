<?php
namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PwaSettings extends Settings
{
    public string $name;
    public string $short_name;
    public string $start_url;
    public string $display;
    public string $background_color;
    public string $theme_color;
    public string $description;
    public array $icons;

    public static function group(): string
    {
        return 'pwa';
    }
}
