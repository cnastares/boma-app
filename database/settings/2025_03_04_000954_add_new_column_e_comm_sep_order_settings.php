<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('e_commerce.enable_seperate_order_conversion', false);
    }

    public function down(): void
    {
        $this->migrator->delete('e_commerce.enable_seperate_order_conversion');
    }
};
