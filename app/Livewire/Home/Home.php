<?php

namespace App\Livewire\Home;

use App\Models\Category;
use App\Models\Promotion;
use App\Models\Ad;
use App\Models\AdType;
use App\Settings\LocationSettings;
use App\Settings\GeneralSettings;
use App\Settings\HomeSettings;
use App\Settings\SEOSettings;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\On;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Illuminate\Support\Collection;

class Home extends Component
{
    use SEOToolsTrait;

    public $categories;
    public $spotlightAds;
    public $isMobileHidden = false;
    public $freshAds;
    public $popularAds = [];
    public $displayedPopularAdsCategories = [];
    public $activeTab = 'tab1';
    public $adTypes;
    public $selected_ad_type;
    /**
     * Mount the component and fetch initial data.
     */
    public function mount()
    {
        $this->adTypes = AdType::all();
        $this->selected_ad_type = AdType::where('is_default', 1)->first()?->id;

        $this->selected_ad_type ? $this->fetchSelectedMainCategories($this->selected_ad_type) : $this->fetchMainCategories();

        $this->loadSpotlightAds();
        $this->loadFreshAds();
        $this->setSeoData();

        if (auth()->user() && getSubscriptionSetting('status') && getActiveSubscriptionPlan() && getActiveSubscriptionPlan()?->product_performance_analysis) {
            $this->loadPopularAds();
        }

        $this->loadDisplayedPopularAdsCategories();
    }

    public function updated($name, $value)
    {
        if ($name == 'selected_ad_type') {
            $this->fetchSelectedMainCategories($value);
        }
    }

    public function loadCategory($adTypeId, $activeTab)
    {
        $this->activeTab = $activeTab;

        $this->fetchSelectedMainCategories($adTypeId);
    }

    protected function fetchSelectedMainCategories($adTypeId)
    {
        $this->categories = Category::with('subcategories')
            ->where('ad_type_id', $adTypeId)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
    }

    protected function fetchMainCategories()
    {
        $this->categories = Category::with('subcategories')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
    }

    public function loadSpotlightAds()
    {
        $spotlightPromotion = Promotion::find(2);
        if (!$spotlightPromotion) {
            $this->spotlightAds = collect();
            return;
        }


        $spotlightAdsQuery = $this->buildLocationBasedAdQuery($spotlightPromotion->id);


        $currentIndex = Cache::get('spotlight_ad_index', 0);
        $totalSpotlightAds = $spotlightAdsQuery->count();


        $this->spotlightAds = $spotlightAdsQuery->skip($currentIndex)->take(5)->get();
        $spotlightAdsCount = $this->spotlightAds->count();


        $remainingAdsCount = ($spotlightAdsCount > 0) ? ($currentIndex + ($spotlightAdsCount)) % $totalSpotlightAds  : 0;
        if ($spotlightAdsCount < 5 && $currentIndex !== 0) {
            $remainingAds = $spotlightAdsQuery->skip($remainingAdsCount)->take(5 - $this->spotlightAds->count())->get();
            $this->spotlightAds = $this->spotlightAds->concat($remainingAds);
        }

        Cache::put('spotlight_ad_index', $remainingAdsCount, now()->addDay());
    }

    /**
     *
     * Load the Top 10 popular ads based on the specific metric
     *
     * Popularity is determined by the number of conversations, view count,
     * number of times added to favorites, and the total time spent on the ad's page.
     *
     * The function sorts the ads in the following priority:
     *  1. Highest number of conversations
     *  2. Highest view count
     *  3. Highest favorite count
     *  4. Highest total time spent on the page
     * @return void
     */
    public function loadPopularAds()
    {
        $this->popularAds = Ad::active()
            ->popular()
            ->take(10)
            ->get();
    }

    #[On('location-updated')]
    public function onLocationUpdated()
    {
        Cache::forget('spotlight_ad_index');
        $this->loadSpotlightAds();
        sleep(1);
        $this->js('location.reload()');
    }

    public function loadFreshAds()
    {
        $this->freshAds = $this->buildLocationBasedAdQuery()
            ->orderBy('posted_date', 'desc')
            ->take(10)
            ->get();
    }

    protected function buildLocationBasedAdQuery($promotionId = null)
    {
        $latitude = session('latitude', null);
        $longitude = session('longitude', null);
        $search_radius = app(LocationSettings::class)->search_radius;
        $locationType = session('locationType', null);
        $selectedCountry = session('country', null);
        $selectedState = session('state', null);
        $selectedCity = session('city', null);

        $query = Ad::where('status', 'active')
            ->when($promotionId, function ($query) use ($promotionId) {
                $query->whereHas('promotions', fn($q) => $q->where('promotion_id', $promotionId)
                    ->where('start_date', '<=', now())->where('end_date', '>=', now()));
            })->when($locationType === 'country' && $selectedCountry, function ($query) use ($selectedCountry) {
                $query->where(function ($query) use ($selectedCountry) {
                    $query->where('country', $selectedCountry)->orWhereNull('country');
                });
            })->when($locationType === 'state' && $selectedState, function ($query) use ($selectedState) {
                $query->where(function ($query) use ($selectedState) {
                    $query->where('state', $selectedState)->orWhereNull('state');
                });
            })->when($locationType === 'city' && $selectedCity, function ($query) use ($selectedCity) {
                $query->where(function ($query) use ($selectedCity) {
                    $query->where('city', $selectedCity)->orWhereNull('city');
                });
            })->when($locationType === 'area' && $latitude && $longitude, function ($query) use ($latitude, $longitude, $search_radius) {
                $query = $query->selectRaw("*, (6371 * acos(cos(radians(?))
                * cos(radians(latitude))
                * cos(radians(longitude)
                - radians(?))
                + sin(radians(?))
                * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
                    ->having('distance', '<', $search_radius)
                    ->orderBy('distance', 'ASC');
            });

        return $query;
    }

    public function getPopularAdsBasedOnCategory($categoryId)
    {
        return Ad::active()
            ->whereHas('category', function ($query) use ($categoryId) {
                $query->where('parent_id', $categoryId);
            })
            ->popular()
            ->take(10)
            ->get();
    }

    public function loadDisplayedPopularAdsCategories()
    {
        $this->displayedPopularAdsCategories = app(HomeSettings::class)->displayed_popular_categories;
    }

    public function placeholder()
    {
        return view('livewire.placeholders.home-skeleton');
    }

    public function getHomeSettingsProperty()
    {
        return app(HomeSettings::class);
    }

    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);
        $siteName = $generalSettings->site_name ?? app_name();
        $siteTagline = $generalSettings->site_tagline ?? '';

        $title = $seoSettings->meta_title ?? "$siteName: $siteTagline";
        $description = $seoSettings->meta_description ?? app_name();
        $ogImage = getSettingMediaUrl('seo.ogimage', 'seo', asset('images/ogimage.jpg'));

        $this->seo()->setTitle($title)->setDescription($description)->setCanonical(url()->current());
        $this->seo()->opengraph()->setTitle($title)->setDescription($description)->setUrl(url()->current())->setType('website')->addImage($ogImage);
        $this->seo()->twitter()->setImage($ogImage)->setUrl(url()->current())->setSite("@" . $seoSettings->twitter_username)->addValue('card', 'summary_large_image');
        $this->seo()->metatags()->addMeta('fb:page_id', $seoSettings->facebook_page_id, 'property')
            ->addMeta('fb:app_id', $seoSettings->facebook_app_id, 'property')
            ->addMeta('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1', 'name');
        $this->seo()->jsonLd()->setTitle($title)->setDescription($description)->setUrl(url()->current())->setType('WebSite');
    }

    public function render()
    {
        return view('livewire.home.home');
    }
}
