<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('offlinePayment.payment_type', []); // Default to empty array
    }

    public function down(): void
    {
        $this->migrator->delete('offlinePayment.payment_type');
    }
};
