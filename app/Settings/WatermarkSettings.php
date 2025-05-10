<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class WatermarkSettings extends Settings
{
    public bool $enable_watermark;
    public string $position;
    public string $watermark_image;

    public static function group(): string
    {
        return 'watermark';
    }
}
