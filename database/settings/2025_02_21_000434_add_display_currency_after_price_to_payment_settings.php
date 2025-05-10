<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.display_currency_after_price', false); // Default: before price
    }

    public function down(): void
    {
        $this->migrator->delete('payment.display_currency_after_price');
    }
};
