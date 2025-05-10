<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.tax_type', 'percentage');
    }

    public function down(): void
    {
        $this->migrator->delete('payment.tax_type');
    }
};
