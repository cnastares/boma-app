<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('stripe.name', 'Stripe');
        $this->migrator->add('stripe.status', false);
        $this->migrator->add('stripe.logo', null);
        $this->migrator->add('stripe.currency', 'USD');
        $this->migrator->add('stripe.public_key', null);
        $this->migrator->add('stripe.secret_key', null);
        $this->migrator->add('stripe.exchange_rate', 1);
    }

    public function down(): void
    {
        $this->migrator->delete('stripe.name');
        $this->migrator->delete('stripe.status');
        $this->migrator->delete('stripe.logo'); 
        $this->migrator->delete('stripe.currency');
        $this->migrator->delete('stripe.public_key');
        $this->migrator->delete('stripe.secret_key');
        $this->migrator->delete('stripe.exchange_rate');
    }
};
