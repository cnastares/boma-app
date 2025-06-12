<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LocationSettings extends Settings
{

    /**
     * Countries that users can select from.
     *
     * Defaults to an empty array so the application can boot
     * even before the settings migrations are executed.
     */
    public array $allowed_countries = [];

    /**
     * ISO code of the default country.
     */
    public string $default_country = 'US';

    /**
     * States that users can select from.
     */
    public array $allowed_states = [];

    /**
     * Default search radius used when filtering locations (in km).
     */
    public int $search_radius = 100;

    /**
     * Automatically detect user location on the frontend.
     */
    public bool $enable_location_auto_detection = false;

    /**
     * Data source for location queries.
     */
    public string $location_source = 'openstreet';

    public static function group(): string
    {
        return 'location';
    }
}
