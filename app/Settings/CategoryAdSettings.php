<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CategoryAdSettings extends Settings
{
    public string $image_width;
    public string $image_height;

    public static function group(): string
    {
        return 'category-ad';
    }
}
