<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('scripts.custom_script_head', '');
        $this->migrator->add('scripts.custom_script_body', '');
    }

    public function down(): void
    {
        $this->migrator->delete('scripts.custom_script_head');
        $this->migrator->delete('scripts.custom_script_body');
    }
};
