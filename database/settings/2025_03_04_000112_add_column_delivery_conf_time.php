<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('wallet_system.delivery_confirmation_time', 14);
    }

    public function down(): void
    {
        $this->migrator->delete('wallet_system.delivery_confirmation_time');
    }
};
