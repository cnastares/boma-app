<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('appearance.enable_contrast_toggle',false);
        $this->migrator->add('appearance.contrast_mode','light_dark');
    }
    public function down(): void
    {
        $this->migrator->delete('appearance.enable_contrast_toggle');
        $this->migrator->delete('appearance.contrast_mode');
    }
};
