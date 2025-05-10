<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('login_otp.enabled', false);
        $this->migrator->add('login_otp.twilio_sid', null);
        $this->migrator->add('login_otp.auth_token', null);
        $this->migrator->add('login_otp.from_number', null);
        $this->migrator->add('login_otp.otp_verification', true);
        $this->migrator->add('login_otp.email_verification', false);
        $this->migrator->add('login_otp.message_customization', 'Your OTP is: ');
    }

    public function down(): void
    {
        $this->migrator->delete('login_otp.enabled');
        $this->migrator->delete('login_otp.twilio_sid');
        $this->migrator->delete('login_otp.auth_token');
        $this->migrator->delete('login_otp.from_number');
        $this->migrator->delete('login_otp.otp_verification');
        $this->migrator->delete('login_otp.email_verification');
        $this->migrator->delete('login_otp.message_customization');
    }
};
