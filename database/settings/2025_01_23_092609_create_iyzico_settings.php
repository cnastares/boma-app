<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('iyzico.name', 'Iyzipay');
        $this->migrator->add('iyzico.status', true);
        $this->migrator->add('iyzico.api_key', '');
        $this->migrator->add('iyzico.secret_key', '');
        $this->migrator->add('iyzico.mode', 'sandbox');
    }

    public function down(): void
    {
        $this->migrator->delete('iyzico.name');
        $this->migrator->delete('iyzico.status');
        $this->migrator->delete('iyzico.api_key');
        $this->migrator->delete('iyzico.secret_key');
        $this->migrator->delete('iyzico.mode');
    }
};
