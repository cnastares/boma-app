<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('offlinePayment.name', 'Bank Transfer');
        $this->migrator->add('offlinePayment.status', false);
        $this->migrator->add('offlinePayment.instruction', null);
        $this->migrator->add('offlinePayment.currency', 'USD');
        $this->migrator->add('offlinePayment.exchange_rate', 1);
    }

    public function down(): void
    {
        $this->migrator->delete('offlinePayment.name');
        $this->migrator->delete('offlinePayment.status');
        $this->migrator->delete('offlinePayment.instruction');
        $this->migrator->delete('offlinePayment.currency');
        $this->migrator->delete('offlinePayment.exchange_rate');
    }
};
