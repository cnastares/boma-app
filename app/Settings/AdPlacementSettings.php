<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AdPlacementSettings extends Settings
{
    public string $after_header;
    public string $before_footer;

    public static function group(): string
    {
        return 'ad_placements';
    }

}
