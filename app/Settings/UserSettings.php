<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class UserSettings extends Settings
{

    public bool $can_edit_registered_email;
    public int $max_character;

    public static function group(): string
    {
        return 'user';
    }
}
