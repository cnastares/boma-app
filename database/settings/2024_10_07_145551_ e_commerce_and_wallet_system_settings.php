<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('wallet_system.enable_wallet_system', false);
        $this->migrator->add('wallet_system.enable_commission', false);
        $this->migrator->add('wallet_system.commission_value', 10);
        $this->migrator->add('wallet_system.commission_type', 'percentage');

        $this->migrator->add('e_commerce.enable_e_commerce', false);
        $this->migrator->add('e_commerce.enable_pay_now', false);
        $this->migrator->add('e_commerce.enable_add_to_cart', false);
        $this->migrator->add('e_commerce.enable_quantity_option', false);
        $this->migrator->add('e_commerce.maximum_quantity_per_item', false);
    }

    public function down(): void
    {
        $this->migrator->delete('wallet_system.enable_wallet_system');
        $this->migrator->delete('wallet_system.enable_commission');
        $this->migrator->delete('wallet_system.commission_value');
        $this->migrator->delete('wallet_system.commission_type');

        $this->migrator->delete('e_commerce.enable_e_commerce');
        $this->migrator->delete('e_commerce.enable_pay_now');
        $this->migrator->delete('e_commerce.enable_add_to_cart');
        $this->migrator->delete('e_commerce.enable_quantity_option');
        $this->migrator->delete('e_commerce.maximum_quantity_per_item');
    }
};
