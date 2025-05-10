<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('asaas.name', 'Asaas');
        $this->migrator->add('asaas.status', false);
        $this->migrator->add('asaas.logo', null);
        $this->migrator->add('asaas.currency', 'BRL');
        $this->migrator->add('asaas.api_key', null);
        $this->migrator->add('asaas.access_token', null);
        $this->migrator->add('asaas.exchange_rate', 1);
        $this->migrator->add('asaas.environment', 'sandbox');
    }

    public function down(): void
    {
        $this->migrator->delete('asaas.name');
        $this->migrator->delete('asaas.status');
        $this->migrator->delete('asaas.logo');
        $this->migrator->delete('asaas.currency');
        $this->migrator->delete('asaas.api_key');
        $this->migrator->delete('asaas.access_token');
        $this->migrator->delete('asaas.exchange_rate');
        $this->migrator->delete('asaas.environment');
    }
};
