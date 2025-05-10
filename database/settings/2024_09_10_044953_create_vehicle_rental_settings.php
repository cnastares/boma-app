<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        // $this->migrator->add('vehicle-rental.enable_down_payment', false);
        $this->migrator->add('vehicle-rental.enable_booking_fee', false);
        $this->migrator->add('vehicle-rental.enable_whatsapp', false);
        $this->migrator->add('vehicle-rental.booking_fee_type', 'percentage');
        $this->migrator->add('vehicle-rental.booking_fee_value', 10);
    }

    public function down(): void
    {
        $this->migrator->delete('vehicle_rental.booking_fees_per_listing');
        $this->migrator->delete('vehicle_rental.global_booking_fee_enabled');
        $this->migrator->delete('vehicle-rental.enable_whatsapp');
        $this->migrator->delete('vehicle_rental.booking_fee_type');
        $this->migrator->delete('vehicle_rental.booking_fee_value');
    }
};
