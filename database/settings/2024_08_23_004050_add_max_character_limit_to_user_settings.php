<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('user.max_character',45);
    }
    public function down(): void
    {
        $this->migrator->delete('user.max_character');
    }
};
