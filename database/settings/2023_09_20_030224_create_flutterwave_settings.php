<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('flutterwave.name', 'Flutterwave');
        $this->migrator->add('flutterwave.status', false);
        $this->migrator->add('flutterwave.logo', null);
        $this->migrator->add('flutterwave.currency', 'NGN');
        $this->migrator->add('flutterwave.public_key', null);
        $this->migrator->add('flutterwave.secret_key', null);
        $this->migrator->add('flutterwave.exchange_rate', 1);
        $this->migrator->add('flutterwave.encryption_key', null); // New field
        $this->migrator->add('flutterwave.environment', 'sandbox'); // New field
    }

    public function down(): void
    {
        $this->migrator->delete('flutterwave.name');
        $this->migrator->delete('flutterwave.status');
        $this->migrator->delete('flutterwave.logo');
        $this->migrator->delete('flutterwave.currency');
        $this->migrator->delete('flutterwave.public_key');
        $this->migrator->delete('flutterwave.secret_key');
        $this->migrator->delete('flutterwave.exchange_rate');
        $this->migrator->delete('flutterwave.encryption_key'); // New field
        $this->migrator->delete('flutterwave.environment'); // New field
    }
};
