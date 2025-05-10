<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PhoneSettings extends Settings
{
    public bool $enable_phone;
    public bool $enable_number_reveal_duplicate;
    public bool $enable_login_user_number_reveal;
    public bool $enable_whatsapp;

    public static function group(): string
    {
        return 'phone';
    }
}
