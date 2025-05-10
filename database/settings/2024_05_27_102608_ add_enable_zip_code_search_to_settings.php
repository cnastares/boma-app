<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('google_location_kit.enable_zip_code_search', false);
    }

    public function down(): void
    {
        $this->migrator->delete('google_location_kit.enable_zip_code_search');
    }
};
