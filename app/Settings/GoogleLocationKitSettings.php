<?php
namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GoogleLocationKitSettings extends Settings
{
    public bool $status;
    public ?string $api_key;
    public bool $enable_zip_code_search;

    public static function group(): string
    {
        return 'google_location_kit';
    }
}
