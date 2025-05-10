<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('map-view.map_marker_display_type','price');
    }
    public function down(): void
    {
        $this->migrator->delete('map-view.map_marker_display_type');
    }
};
