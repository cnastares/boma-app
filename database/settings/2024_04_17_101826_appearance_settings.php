<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('appearance.primary_color', '#FDae4B');
        $this->migrator->add('appearance.secondary_color', '#FEBD69');
        $this->migrator->add('appearance.enable_theme_switcher', true);
        $this->migrator->add('appearance.default_theme', 'classic');
        $this->migrator->add('appearance.home_banner_image', '');
    }

    public function down(): void
    {
        $this->migrator->delete('appearance.primary_color');
        $this->migrator->delete('appearance.secondary_color');
        $this->migrator->delete('appearance.enable_theme_switcher');
        $this->migrator->delete('appearance.default_theme');
        $this->migrator->delete('appearance.home_banner_image');
    }
};
