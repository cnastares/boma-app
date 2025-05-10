<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('appearance.font', 'DM Sans');
    }

    public function down(): void
    {
        $this->migrator->delete('appearance.font');
    }
};
