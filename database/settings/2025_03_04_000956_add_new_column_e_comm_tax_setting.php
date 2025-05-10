<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('e_commerce.enable_tax', false);
        $this->migrator->add('e_commerce.tax_rate', 1);
    }

    public function down(): void
    {
        $this->migrator->delete('e_commerce.enable_tax');
        $this->migrator->delete('e_commerce.tax_rate');
    }
};
