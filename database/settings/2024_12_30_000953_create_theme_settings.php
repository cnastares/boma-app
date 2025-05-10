<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('theme.selected_theme', 'classic'); // Default theme
    }

    public function down(): void
    {
        $this->migrator->delete('theme.selected_theme');
    }
};
