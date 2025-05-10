
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('map-view.enable',false);
        $this->migrator->add('map-view.enable_container_max_width',false);
    }

    public function down(): void
    {
        $this->migrator->delete('map-view.enable');
        $this->migrator->delete('map-view.enable_container_max_width');
    }
};
