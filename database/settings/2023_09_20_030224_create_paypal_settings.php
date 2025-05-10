<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('paypal.name', 'PayPal');
        $this->migrator->add('paypal.status', false);
        $this->migrator->add('paypal.logo', null);
        $this->migrator->add('paypal.currency', 'USD');
        $this->migrator->add('paypal.client_id', null);
        $this->migrator->add('paypal.client_secret', null);
        $this->migrator->add('paypal.exchange_rate', 1);
    }

    public function down(): void
    {
        $this->migrator->delete('paypal.name');
        $this->migrator->delete('paypal.status');
        $this->migrator->delete('paypal.logo');
        $this->migrator->delete('paypal.currency');
        $this->migrator->delete('paypal.client_id');
        $this->migrator->delete('paypal.client_secret');
        $this->migrator->delete('paypal.exchange_rate');
    }
};
