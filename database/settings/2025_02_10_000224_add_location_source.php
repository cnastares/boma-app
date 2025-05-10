<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('location.location_source', 'openstreet');
    }

    public function down(): void
    {
        $this->migrator->delete('location.location_source');
    }
};
