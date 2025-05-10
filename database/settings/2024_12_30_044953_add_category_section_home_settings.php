<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.displayed_popular_categories', []);
    }

    public function down(): void
    {
        $this->migrator->delete('home.displayed_popular_categories');
    }
};
