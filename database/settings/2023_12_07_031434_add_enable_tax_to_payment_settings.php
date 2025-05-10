<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.enable_tax', false);
    }
    public function down(): void
    {
        $this->migrator->delete('payment.enable_tax');
    }
};
