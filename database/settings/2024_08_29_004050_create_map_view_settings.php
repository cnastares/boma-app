
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('map-view.show_filter_popup',false);
    }

    public function down(): void
    {
        $this->migrator->delete('map-view.show_filter_popup');
    }
};
