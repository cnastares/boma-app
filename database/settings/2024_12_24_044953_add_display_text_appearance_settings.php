<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('appearance.display_site_name', false);
    }

    public function down(): void
    {
        $this->migrator->delete('appearance.display_site_name');
    }
};
