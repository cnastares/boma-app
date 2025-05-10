<?php

namespace App\Livewire\Ad\PostAd;

use App\Models\Ad;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use App\Settings\LocationSettings;
use App\Settings\GoogleLocationKitSettings;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\DB;


class LocateAd extends Component implements HasForms
{
    use InteractsWithForms;


    // Properties related to the ad's location
    public $latitude;
    public $longitude;
    public $locationName;
    public $locationDisplayName;
    public $locationFetch = false;
    public $locationBlocked = false;
    public $postal_code;
    public $country_code;
    public $city;
    public $state;
    public $country;
    public $city_id;
    public $state_id;
    public $country_id;
    // Ad ID property
    public $id;
    #[Reactive] public $isLastStep;

    /**
     * Mount the component and set the properties if an ad ID is provided.
     */
    public function mount($id)
    {
        $this->id = $id;
        if ($this->id) {
            $ad = Ad::find($this->id);
            if ($ad) {
                $this->latitude = $ad->latitude;
                $this->longitude = $ad->longitude;
                $this->locationName = $ad->location_name;
                $this->postal_code = $ad->postal_code;
                if ($this->latitude && $this->longitude) {
                    $city = $this->findNearestCity($this->latitude, $this->longitude);
                    if ($city) {
                        $this->city = $city->name;
                        $this->state = $city->state->name;
                        $this->country = $city->country->name;
                        $this->city_id = $city->id;
                        $this->state_id = $city->state_id;
                        $this->country_id = $city->country_id;
                    }
                }
            }
        }
        $this->checkRequiredFieldsFilled();
    }

    public function form(Form $form): Form
    {

        $allowedCountries = $this->locationSettings->allowed_countries ?? [];

        return $form
            ->schema([
                Select::make('country_id')
                    ->label(__('messages.t_country'))
                    ->options(
                        !empty($allowedCountries) ?
                        Country::whereIn('iso2', $allowedCountries)->orderBy('name')->pluck('name', 'id')->toArray() :
                        Country::orderBy('name')->pluck('name', 'id')->toArray()
                    )
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Set $set) {
                        $set('state_id', null);
                        $set('city_id', null);
                        $this->state_id = null;
                        $this->city_id = null;
                        $this->checkRequiredFieldsFilled();

                    })
                    ->required(),

                Select::make('state_id')
                    ->label(__('messages.t_state'))
                    ->options(function (Get $get) {
                        $countryId = $get('country_id');
                        if (!$countryId) {
                            return [];
                        }
                        return State::where('country_id', $countryId)->orderBy('name')->pluck('name', 'id')->toArray();
                    })
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Set $set) {
                        $set('city_id', null);
                        $this->city_id = null;
                        $this->checkRequiredFieldsFilled();
                    })
                    ->required(),

                Select::make('city_id')
                    ->label(__('messages.t_city'))
                    ->options(function (Get $get) {
                        $stateId = $get('state_id');
                        if (!$stateId) {
                            return [];
                        }
                        return City::where('state_id', $stateId)->orderBy('name')->pluck('name', 'id')->toArray();
                    })
                    ->live(debounce: 500)
                    ->required()


            ]);
    }

    /**
     * When certain properties are updated, update the location.
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['postal_code', 'latitude', 'longitude', 'locationName', 'locationDisplayName', 'country', 'state', 'city'])) {
            $this->updateLocation();
        }
        if ($propertyName === 'city_id') {
            $this->updateLocationFromCityId();
        }
        $this->checkRequiredFieldsFilled();
    }

    public function getGoogleSettingsProperty()
    {
        return app(GoogleLocationKitSettings::class);
    }

    public function getLocationSettingsProperty()
    {
        return app(LocationSettings::class);
    }

    public function findNearestCity($latitude = null, $longitude = null)
    {
        // Validate that latitude and longitude are provided
        if (is_null($latitude) || is_null($longitude) || empty($latitude) || empty($longitude)) {
            return null;
        }
        return City::with('state', 'country')
            ->select('cities.*', DB::raw("(6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance"))
            ->orderBy('distance', 'asc')
            ->first();
    }


    /**
     * Update the ad's location details in the database.
     */
    public function updateLocation()
    {
        $ad = Ad::find($this->id);

        $city = $this->findNearestCity($this->latitude, $this->longitude);

        $this->city_id = $city->id;
        $this->state_id = $city->state_id;
        $this->country_id = $city->country_id;

        if (!$ad) {
            abort(404, 'Advertisement not found.');
        }

        $this->authorize('update', $ad);

        $ad->update([
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location_name' => $this->locationName,
            'location_display_name' => $this->locationDisplayName,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
        ]);
    }

    public function updateLocationFromCityId()
    {
        $city = City::with('state', 'country')->find($this->city_id);

        if ($city) {
            $this->latitude = $city->latitude;
            $this->longitude = $city->longitude;
            $this->city = $city->name;
            $this->state = $city->state->name;
            $this->country = $city->country->name;
            $this->locationName = $this->city . ' - ' . $this->state;

            $ad = Ad::find($this->id);
            if ($ad) {
                $ad->update([
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'location_name' => $this->locationName,
                    'location_display_name' => "{$this->city}, {$this->state}, {$this->country}",
                    'city' => $this->city,
                    'state' => $this->state,
                    'country' => $this->country
                ]);
            }
        }
    }

    /**
     * Proceed to the next step after verifying the location details.
     */
    #[On('next-clicked')]
    public function next()
    {
        if (empty($this->city) || empty($this->state) || empty($this->country)) {
            $this->addError('locationName', __('messages.t_location_select_prompt'));
            return;
        }
        if ($this->isLastStep) {
            $ad = Ad::find($this->id);
            if ($ad && $ad->status->value != 'draft') {
                $this->dispatch('preview-ad');
            } else {
                $this->dispatch('publish-clicked');
            }
        } else {
            $this->dispatch('next-step');
        }
    }

    public function checkRequiredFieldsFilled()
    {
        $isFilled = false;
        if (empty($this->locationName) || empty($this->country_id) || empty($this->state_id) || empty($this->city_id)) {
            $isFilled = false;
        } else {
            $isFilled = true;

        }
        $this->dispatch('required-fields-filled', isFilled: $isFilled);
    }
    /**
     * Render the component view.
     */
    public function render()
    {
        // Determine which view to render based on the presence of the 'google-location-kit' plugin
        $view = app('filament')->hasPlugin('google-location-kit') && (app(GoogleLocationKitSettings::class)->status && app(GoogleLocationKitSettings::class)->enable_zip_code_search) ? 'google-location-kit::locate-ad' : 'livewire.ad.post-ad.locate-ad';
        return $view;

    }
}
