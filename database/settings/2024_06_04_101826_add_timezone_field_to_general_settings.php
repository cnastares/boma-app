<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.timezone', 'UTC');
        $this->migrator->add('general.date_format', 'UTC');
    }

    public function down(): void
    {
        $this->migrator->delete('general.timezone');
        $this->migrator->delete('general.date_format');
    }
};
