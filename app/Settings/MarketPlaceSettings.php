<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MarketPlaceSettings extends Settings
{

    public string $business_type;
    public static function group(): string
    {
        return 'marketplace';
    }
}
