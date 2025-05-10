
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.lg_search_box_size',350);
    }

    public function down(): void
    {
        $this->migrator->delete('home.lg_search_box_size');
    }
};
