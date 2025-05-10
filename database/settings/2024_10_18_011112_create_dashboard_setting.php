<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('dashboard-settings.enable_move_chart_to_bottom', false);
    }
    public function down(): void
    {
        $this->migrator->delete('dashboard-settings.enable_move_chart_to_bottom');
    }
};
