<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class OfflinePaymentSettings extends Settings
{
    public ?string $name;
    public bool $status;
    public ?string $instruction;
    public ?string $currency;
    public float $exchange_rate;
    public array $payment_type; // Added payment_type to store 'name' and 'instruction'

    public static function group(): string
    {
        return 'offlinePayment';
    }
}
