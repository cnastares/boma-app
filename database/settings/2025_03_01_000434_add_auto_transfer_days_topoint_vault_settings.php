<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('point_vault.auto_transfer_days', 14); // Default: before price
    }

    public function down(): void
    {
        $this->migrator->delete('point_vault.auto_transfer_days');
    }
};
