<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('auth.enable_google_login', false);
        $this->migrator->add('auth.enable_facebook_login', false);
        $this->migrator->add('auth.recaptcha_enabled', false);
    }

    public function down(): void
    {
        $this->migrator->delete('auth.enable_google_login');
        $this->migrator->delete('auth.enable_facebook_login');
        $this->migrator->delete('auth.recaptcha_enabled');
    }
};
