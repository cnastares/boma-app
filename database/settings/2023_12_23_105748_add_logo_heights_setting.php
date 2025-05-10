<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.logo_height_mobile', '1');
        $this->migrator->add('general.logo_height_desktop', '1.5');
    }

    public function down(): void
    {
        $this->migrator->delete('general.logo_height_mobile');
        $this->migrator->delete('general.logo_height_desktop');
    }
};
