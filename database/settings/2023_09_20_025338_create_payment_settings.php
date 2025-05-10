<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.currency', 'USD');  // Default to USD
        $this->migrator->add('payment.tax_rate', null);  // Default to null (no tax)
    }

    public function down(): void
    {
        $this->migrator->delete('payment.currency');
        $this->migrator->delete('payment.tax_rate');
    }
};
