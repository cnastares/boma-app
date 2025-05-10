<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ScriptSettings extends Settings
{
    public string $custom_script_head;
    public string $custom_script_body;

    public static function group(): string
    {
        return 'scripts';
    }
}
