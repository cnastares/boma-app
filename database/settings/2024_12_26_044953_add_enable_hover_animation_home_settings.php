<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.enable_hover_animation', true);
    }

    public function down(): void
    {
        $this->migrator->delete('home.enable_hover_animation');
    }
};
