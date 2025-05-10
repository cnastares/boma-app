<?php
namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class IyzicoSettings extends Settings
{
    public ?string $name;
    public bool $status;
    public ?string $api_key;
    public ?string $secret_key;
    public ?string $mode;

    public static function group(): string
    {
        return 'iyzico';
    }
}

