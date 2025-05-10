<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AuthSettings extends Settings
{
    public bool $enable_google_login;
    public bool $enable_facebook_login;
    public bool $recaptcha_enabled;
    public array $custom_registration_fields;

    public static function group(): string
    {
        return 'auth';
    }

}
