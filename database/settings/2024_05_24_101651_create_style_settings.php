<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('style.custom_style', '');
    }
    public function down(): void
    {
        $this->migrator->delete('style.custom_style');
    }
};
