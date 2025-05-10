<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification-registration.enable', false);  // Default to no moderation
        $this->migrator->add('notification-registration.notification_email', "");  // Default to no moderation
        $this->migrator->add('notification-registration.instagram_username', "");  // Default to no moderation
    }
    public function down(): void
    {
        $this->migrator->delete('notification-registration.enable');
        $this->migrator->delete('notification-registration.notification_email');
        $this->migrator->delete('notification-registration.instagram_username');
    }
};
