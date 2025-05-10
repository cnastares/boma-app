<?php

namespace App\Livewire\Layout;

use App\Models\City;
use App\Settings\LocationSettings;
use Livewire\Component;
use App\Models\Country;
use App\Settings\GoogleLocationKitSettings;
use Livewire\Attributes\Reactive;

/**
 * Location Component.
 *
 * Represents the location selector and functionalities related to
 * managing and storing user's location preferences.
 */
class Location extends Component
{
    // Latitude of the selected location.
    public $latitude;

    // Longitude of the selected location.
    public $longitude;

    // Display name of the selected location.
    public $locationName = 'All locations';

    // Indicates if the user has blocked the location access.
    public $locationBlocked = false;

    public $locationFetch = false;

    #[Reactive]
    public $locationSlug;

    public $canAutoDetect = false;

    public $locationInput;
    public $customLocationResults = [];
    /**
     * Initialize the component.
     *
     * Set the default location based on the system settings or user's session.
     */
    public function mount()
    {
        $default_country = app(LocationSettings::class)->default_country;

        $this->locationName = session('locationName', $this->locationName) ?? null;

        if (empty($this->locationName) && !empty($default_country)) {
            $this->locationName = Country::where('iso2', $default_country)->value('name');
        }

        if ($this->locationSettings->enable_location_auto_detection) {
            $this->checkLocationAutoDetection();
        }
    }

    public function checkLocationAutoDetection()
    {
        $this->canAutoDetect = !(session('latitude') && session('longitude'));
    }

    /**
     * Store the selected location details in the user's session.
     *
     * @param float  $latitude
     * @param float  $longitude
     * @param string $locationName
     * @param string $country
     * @param string $state
     * @param string $city
     * @param string $locationType
     */
    public function storeLocationInSession($latitude, $longitude, $locationName, $country, $state, $city, $locationType)
    {
        session([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'locationName' => $locationName,
            'country' => $country,
            'state' => $state,
            'city' => $city,
            'locationType' => $locationType
        ]);

        $this->dispatch('location-updated');
    }

    public function getGoogleSettingsProperty()
    {
        return app(GoogleLocationKitSettings::class);
    }

    public function getLocationSettingsProperty()
    {
        return app(LocationSettings::class);
    }

    /**
     * Update the component's latitude and longitude properties based on selected location.
     *
     * @param array $data Associative array containing 'latitude' and 'longitude' keys.
     */
    public function selectLocation($data)
    {
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
    }

    /**
     * Handle the updated event for the 'locationInput' property.
     * @param mixed $property
     * @param mixed $value
     * @return void
     */
    public function updated($property, $value)
    {
        if ($property == 'locationInput' && trim($value)) {
            $this->locationFetch = true;
            $this->searchManualLocation($value);
        }
    }

    /**
     * Search for cities based on the provided value.
     * @param mixed $value
     * @return void
     */
    public function searchManualLocation($value)
    {
        $allowedCountries = app(LocationSettings::class)->allowed_countries ?? [];

        $this->customLocationResults = City::where('name', 'like', "%$value%")
            ->when(!empty($allowedCountries), function ($query) use ($allowedCountries) {
                $query->whereHas('country', function ($query) use ($allowedCountries) {
                    return $query->whereIn('iso2', $allowedCountries);
                });
            })
            ->with(['state', 'country'])
            ->limit(5)
            ->get()
            ->mapWithKeys(fn($city) => [$city->id => $this->formatCityResult($city)])
            ->toArray();
    }

    /**
     * Format the city result for display.
     * @param mixed $city
     * @return string
     */
    public function formatCityResult($city)
    {
        return implode(', ', array_filter([
            $city->name,
            $city->state_name,
            $city->country_name,
        ]));
    }

    /**
     * Update location details based on the selected city.
     * @param mixed $id
     * @return void
     */
    public function selectManualLocation($id)
    {
        $city = City::with('state', 'country')->find($id);
        if ($city) {
            $this->locationName = $city->name;
            $this->latitude = $city->latitude;
            $this->longitude = $city->longitude;
            $this->locationBlocked = false;
            $this->locationFetch = false;
            $this->storeLocationInSession($city->latitude, $city->longitude, $city->name, $city->country->name, $city->state->name, $city->name, 'city');
        }
        $this->locationInput = '';
    }
    /**
     * Render the location view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {

        $locationSettings = app(LocationSettings::class);

        switch ($locationSettings->location_source) {
            case 'google':
                // Check if Google Location Kit is enabled
                if (app('filament')->hasPlugin('google-location-kit') && app(GoogleLocationKitSettings::class)->status) {
                    return view('google-location-kit::layout.location');
                }
                return view('livewire.layout.locations.open-street-map');

            case 'openstreet':
                return view('livewire.layout.locations.open-street-map');

            case 'custom':
                return view('livewire.layout.locations.custom-location');

            default:
                return view('livewire.layout.locations.open-street-map'); // Fallback view
        }
    }

}
