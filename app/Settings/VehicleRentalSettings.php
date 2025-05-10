<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class VehicleRentalSettings extends Settings
{

    public bool $enable_down_payment;
    public string $down_payment_type;
    public int $down_payment_value;
    public string $booking_fee_type;
    public int $booking_fee_value;
    public bool $enable_whatsapp;
    public int $time_interval;

    public static function group(): string
    {
        return 'vehicle-rental';
    }
}
