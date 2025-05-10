<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payu.name', 'PayU');
        $this->migrator->add('payu.status', false);
        // $this->migrator->add('payu.merchantId', '4A90Vu');
        // $this->migrator->add('payu.saltKey', 'XAMUCRInvWbgM9lwSUoRBbQyTuvboacP');
        // $this->migrator->add('payu.env_mode', "test");
        $this->migrator->add('payu.exchange_rate', 1);

    }

    public function down(): void
    {
        $this->migrator->delete('payu.name');
        $this->migrator->delete('payu.status');
        // $this->migrator->delete('payu.merchantId');
        // $this->migrator->delete('payu.saltKey');
        // $this->migrator->delete('payu.env_mode');
        $this->migrator->delete('payu.exchange_rate', 1);
    }
};
