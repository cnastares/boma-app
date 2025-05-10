<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ad_list.sort_by_position', 'filter_box'); // Default Position
    }

    public function down(): void
    {
        $this->migrator->delete('ad_list.sort_by_position');
    }
};
