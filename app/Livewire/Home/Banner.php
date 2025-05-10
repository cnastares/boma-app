<?php

namespace App\Livewire\Home;

use App\Models\Banner as ModelsBanner;
use App\Models\BannerAnalytics;
use App\Settings\BannerSettings;
use App\Settings\LocationSettings;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Banner extends Component
{
    public $search;
    public $banners;

    public function mount()
    {
        if (app('filament')->hasPlugin('appearance')) {
            $this->loadBannerDetails();
        }
    }
    public function getBannerSettingsProperty()
    {
        return app(BannerSettings::class);
    }

    /**
     * Load banner details.
     *
     * @return void
     */
    public function loadBannerDetails()
    {
        $latitude = session('latitude', null);
        $longitude = session('longitude', null);
        $search_radius = app(LocationSettings::class)->search_radius ?? 100;

        $query = ModelsBanner::query()->orderBy('order', 'asc');

        if ($latitude && $longitude) {
            // Try city level banners first with radius filter
            $cityQuery = clone $query;
            $cityBanners = $this->getCitiesBanner($cityQuery, $search_radius, 'city_id', 'cities', $latitude, $longitude);
            if (count($cityBanners) > 0) {
                $this->banners = $cityBanners;
                return;
            }

            // Get state level banners for the user's current state
            $stateQuery = clone $query;
            $stateBanners = $this->getLocationBasedBanner($stateQuery, $search_radius, 'state_id', $latitude, $longitude);
            if (count($stateBanners) > 0) {
                $this->banners = $stateBanners;
                return;
            }

            // Get country level banners for the user's current country
            $countryQuery = clone $query;
            $countryBanners = $this->getLocationBasedBanner($countryQuery, $search_radius, 'country_id', $latitude, $longitude);
            if (count($countryBanners) > 0) {
                $this->banners = $countryBanners;
                return;
            }
        }

        $this->banners = $this->getGlobalBanners($query);
    }

    /**
     * Get global banners where city, state, and country are null.
     * @param mixed $query
     * @return mixed
     */
    public function getGlobalBanners($query){
        return $query->whereNull('city_id')->whereNull('state_id')->whereNull('country_id')->get();
    }

    /**
     * return the haversine formula based on relationship and lat/long
     * @param mixed $searchRelationship
     * @param mixed $latitude
     * @param mixed $longitude
     * @return string
     */
    public function getHaversine($searchRelationship, $latitude, $longitude)
    {
        return "(
            6371 * acos(
                cos(radians($latitude))
                * cos(radians({$searchRelationship}.latitude))
                * cos(radians({$searchRelationship}.longitude) - radians($longitude))
                + sin(radians($latitude))
                * sin(radians({$searchRelationship}.latitude))
            )
        )";
    }

    public function getCitiesBanner($query, $search_radius, $searchId, $searchRelationship, $latitude, $longitude){

        return $query->whereNotNull($searchId)
                ->join($searchRelationship, "banners.{$searchId}", '=', "{$searchRelationship}.id")
                ->select('banners.*')
                ->selectRaw("{$this->getHaversine($searchRelationship, $latitude, $longitude)} AS distance")
                ->having('distance', '<=', $search_radius)
                ->orderBy('distance')
                ->get();
    }

    public function getLocationBasedBanner($query, $search_radius, $searchId, $latitude, $longitude, $useRadius = true)
    {

        // For state and country level, find the user's current location first
        $currentLocation = DB::table('cities')
            ->select('cities.state_id', 'states.country_id')
            ->join('states', 'cities.state_id', '=', 'states.id')
            ->whereRaw("{$this->getHaversine('cities', $latitude, $longitude)} <= ?", [$search_radius])
            ->orderBy(DB::raw($this->getHaversine('cities', $latitude, $longitude)))
            ->first();

            if (!$currentLocation) {
            return collect();
            }

        // For state level banners
        if ($searchId === 'state_id') {
            return $query->whereNotNull('state_id')->whereNull('city_id')
                ->where('state_id', $currentLocation->state_id)
                ->get();
        }

        // For country level banners
        if ($searchId === 'country_id') {
            return $query->whereNotNull('country_id')->whereNull('city_id')->whereNull('state_id')
                ->where('country_id', $currentLocation->country_id)
                ->get();
        }

        return collect();
    }

    public function updateClickCount($bannerId)
    {
        $banner = ModelsBanner::whereId($bannerId)->first();
        if ($banner) {
            BannerAnalytics::create([
                'banner_id' => $bannerId,
                'event' => 'click'
            ]);
            // $banner->increment('clicks');
            $link = $banner->link;
            if ($link) {
                return $this->js("setTimeout(() => {window.open(" . "'" . $link . "'" . ", '_blank')})");
            }
        }
    }
    #[On('update-banner-view')]
    public function updateBannerView($bannerId)
    {
        BannerAnalytics::create([
            'banner_id' => $bannerId,
            'event' => 'view'
        ]);
    }
    /**
     * Perform a search based on the user's query.
     *
     * This function will trim the search query, check if it's not empty, and then
     * redirect the user to the search results page.
     */
    public function performSearch()
    {
        // Trim white spaces from the search query.
        $this->search = trim($this->search);

        // Check if the search query is not empty.
        if (empty($this->search)) {
            // Optional: Notify the user that the search query cannot be empty.
            // Notification::make()->title('Search query cannot be empty.')->warning()->send();
            return;
        }

        // Construct the search URL with query parameters.
        $searchUrl = url('search') . '?query[sortBy]=date&query[search]=' . urlencode($this->search);

        // Redirect to the constructed URL.
        return redirect()->to($searchUrl);
    }


    /**
     * Returns the header placeholder view.
     *
     * @return \Illuminate\View\View
     */
    public function placeholder()
    {
        return view('livewire.placeholders.banner-skeleton');
    }

    /**
     * Render the banner view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        //Check if the appearance plugin is installed and if the banner settings are enabled and if there are any banners
        $view = (app('filament')->hasPlugin('appearance') && $this->bannerSettings && $this->bannerSettings?->enable_carousel) && $this->banners && count($this->banners) ? 'appearance::home.banner-carousel' : 'livewire.home.banner';
        return view($view);

    }
}
