<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('auth.custom_registration_fields', []);
    }

    public function down(): void
    {
        $this->migrator->delete('auth.custom_registration_fields');
    }
};
