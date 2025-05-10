<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('location.enable_location_auto_detection', false);
    }

    public function down(): void
    {
        $this->migrator->delete('location.enable_location_auto_detection');
    }
};
