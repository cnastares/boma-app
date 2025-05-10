<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('vehicle-rental.enable_down_payment', false);
        $this->migrator->add('vehicle-rental.down_payment_type', 'percentage');
        $this->migrator->add('vehicle-rental.down_payment_value', 10);

    }

    public function down(): void
    {
        $this->migrator->delete('vehicle-rental.enable_down_payment');
        $this->migrator->delete('vehicle_rental.down_payment_type');
        $this->migrator->delete('vehicle_rental.down_payment_value');
    }
};
