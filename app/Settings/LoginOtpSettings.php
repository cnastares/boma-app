<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LoginOtpSettings extends Settings
{
    public bool $enabled;
    public ?string $twilio_sid;
    public ?string $auth_token;
    public ?string $from_number;
    public bool $otp_verification;
    public bool $email_verification;
    public string $message_customization;

    public static function group(): string
    {
        return 'login_otp';
    }
}
