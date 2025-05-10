<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MapViewSettings extends Settings
{

    public bool $show_filter_popup;
    public bool $enable;
    public bool $enable_container_max_width;
    public string $map_marker_display_type;
    public bool $show_map_in_fullscreen;

    public static function group(): string
    {
        return 'map-view';
    }
}
