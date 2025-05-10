<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('razorpay.name', 'Razorpay');
        $this->migrator->add('razorpay.status', false);
        $this->migrator->add('razorpay.logo', null);
        $this->migrator->add('razorpay.currency', 'INR');
        $this->migrator->add('razorpay.key_id', '');
        $this->migrator->add('razorpay.key_secret', '');
        $this->migrator->add('razorpay.exchange_rate', 1);

    }

    public function down(): void
    {
        $this->migrator->delete('razorpay.name');
        $this->migrator->delete('razorpay.logo');
        $this->migrator->delete('razorpay.status');
        $this->migrator->delete('razorpay.currency');
        $this->migrator->delete('razorpay.key_id');
        $this->migrator->delete('razorpay.key_secret');
        $this->migrator->delete('razorpay.exchange_rate');

    }
};
