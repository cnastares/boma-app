<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MaintenanceSettings extends Settings
{
    public ?string $headline;
    public ?string $message;
    public ?string $secret;

    public static function group(): string
    {
        return 'maintenance';
    }
}
