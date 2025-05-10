<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('point_vault.buy_point_sidebar_text', ''); // Default: before price
    }

    public function down(): void
    {
        $this->migrator->delete('point_vault.buy_point_sidebar_text');
    }
};
