<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AdListSettings extends Settings
{
    public string $sort_by_position;


    public static function group(): string
    {
        return 'ad_list';
    }

}
