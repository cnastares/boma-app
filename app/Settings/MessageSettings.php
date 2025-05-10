<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MessageSettings extends Settings
{
    public bool $enable_make_offer;

    public static function group(): string
    {
        return 'message';
    }

}
