<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('phonepe.name', 'Phonepe');
        $this->migrator->add('phonepe.status', false);
        $this->migrator->add('phonepe.merchantId', '');
        $this->migrator->add('phonepe.saltKey', '');
        $this->migrator->add('phonepe.saltIndex', 1);
        $this->migrator->add('phonepe.env_mode', "UAT");
        $this->migrator->add('phonepe.exchange_rate', 1);
    }

    public function down(): void
    {
        $this->migrator->delete('phonepe.name');
        $this->migrator->delete('phonepe.status');
        $this->migrator->delete('phonepe.merchantId');
        $this->migrator->delete('phonepe.saltKey');
        $this->migrator->delete('phonepe.saltIndex');
        $this->migrator->delete('phonepe.env_mode');
        $this->migrator->delete('phonepe.exchange_rate', 1);
    }
};
