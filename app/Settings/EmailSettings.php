<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class EmailSettings extends Settings
{
    public bool $display_logo;
    public string $email_logo;
    public static function group(): string
    {
        return 'email';
    }
}
