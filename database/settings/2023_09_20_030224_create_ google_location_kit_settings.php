<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('google_location_kit.status', false);
        $this->migrator->add('google_location_kit.api_key', null);
    }

    public function down(): void
    {
        $this->migrator->delete('google_location_kit.status');
        $this->migrator->delete('google_location_kit.api_key');
    }
};
