<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BannerSettings extends Settings
{
    public bool $enable_carousel;
    public array $banner_data;
    public bool $enable_autoplay;
    public bool $enable_pagination_count;
    public int $autoplay_interval;

    public static function group(): string
    {
        return 'banner';
    }
}
