<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class UiCustomizationSettings extends Settings
{
    public array $ad_detail_page;

    public static function group(): string
    {
        return 'ui_customization';
    }
}
