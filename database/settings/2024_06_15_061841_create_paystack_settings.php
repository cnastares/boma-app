<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('paystack.name', 'Paystack');
        $this->migrator->add('paystack.status', false);
        $this->migrator->add('paystack.currency', 'NGN');
        $this->migrator->add('paystack.secret_key', '');
        $this->migrator->add('paystack.public_key', '');
        $this->migrator->add('paystack.exchange_rate', 1);

    }

    public function down(): void
    {
        $this->migrator->delete('paystack.name');
        $this->migrator->delete('paystack.status');
        $this->migrator->delete('paystack.currency');
        $this->migrator->delete('paystack.secret_key');
        $this->migrator->delete('paystack.public_key');
        $this->migrator->delete('paystack.exchange_rate');

    }
};
