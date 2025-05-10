<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('location.allowed_countries', []);
        $this->migrator->add('location.default_country', 'US');
        $this->migrator->add('location.allowed_states', []);
        $this->migrator->add('location.search_radius', 100);
    }

    public function down(): void
    {
        $this->migrator->delete('location.allowed_countries');
        $this->migrator->delete('location.default_country');
        $this->migrator->delete('location.allowed_states');
        $this->migrator->delete('location.search_radius');
    }
};
