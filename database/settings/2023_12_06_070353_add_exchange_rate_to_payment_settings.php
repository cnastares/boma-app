<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.exchange_rate', 1); // Default value as 1.0
    }

    public function down(): void
    {
        $this->migrator->delete('payment.exchange_rate');
    }
};
