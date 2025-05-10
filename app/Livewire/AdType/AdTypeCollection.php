<?php

namespace App\Livewire\AdType;

use App\Models\Ad;
use App\Models\AdPromotion;
use App\Models\AdType;
use App\Models\Category;
use App\Models\CategoryAdPlacement;
use App\Services\CategoryService;
use App\Settings\LocationSettings;
use App\Traits\ChildCategoryTrait;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Settings\GeneralSettings;
use App\Settings\MapViewSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

class AdTypeCollection extends Component
{
    use WithPagination, ChildCategoryTrait;
    use SEOToolsTrait;

    // Properties
    public $metaTitle;
    public $metaDescription;

    public $categorySlug;
    public $subCategorySlug;

    public $locationSlug;

    public $breadcrumbs = [];

    public $isMobileHidden = false;

    #[Url(keep: true, as: "query")]
    public $filters = [
        'sortBy' => 'date',
    ];
    #[Url(keep: true, as: "filter")]
    public ?array $fieldFilter = [];
    #[Url(keep: true, as: "select")]
    public ?array $selectFieldFilter = [];
    #[Url()]
    public $page;
    public $latitude;
    public $longitude;
    public $adType;
    public $currentView = 'grid';
    protected $listeners = ['field-filter-updated' => 'updateFieldFilter'];
    public $adPlacement;
    public $childCategories = [];
    public $childCategorySlug;

    /**
     * Mount the component with given category and optionally subcategory.
     *
     * @param  string  $category      The category slug for filtering.
     * @param  string|null  $subcategory  The subcategory slug for filtering (optional).
     */
    public function mount($type = null, $category = null, $subCategory = null, $childCategory = null, $location = null)
    {
        $this->initializeAdType($type);

        $this->categorySlug = $category ?? null;

        $this->subCategorySlug = $subCategory ?? null;
        $this->childCategorySlug = $childCategory ?? null;
        $this->locationSlug = $location;
        $this->buildBreadcrumbs();
        $this->setSeoData();
        $this->loadAdPlacements();
        $this->loadChildCategories();
    }

    protected function initializeAdType($type)
    {
        if ($type) {
            $this->adType = AdType::where('slug', $type)->first();
        }
    }
    /**
     * Apply Child category filter and redirect to url
     * @param mixed $childCategoryId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function selectChildCategory($childCategoryId)
    {

        $url = $this->getChildCategoryUrl($childCategoryId);

        // Get the current query string
        $queryString = request()->query();

        // Append the query string if it exists
        return redirect($queryString ? "{$url}?{$queryString}" : $url);
    }

    /**
     * clear child category filter
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function clearChildCategory()
    {

        $this->childCategorySlug = null;
        $url = $this->getSubCategoryUrl();

        // Get the current query string
        $queryString = request()->query();

        // Append the query string if it exists
        return redirect($queryString ? "{$url}?{$queryString}" : $url);
    }

    public function loadAdPlacements()
    {
        if($this->categorySlug || $this->subCategorySlug )
        {
            $this->adPlacement = CategoryAdPlacement::getRelevantAdPlacements($this->categorySlug, $this->subCategorySlug)->first();
        }
    }

    public function getFormattedAdsProperty()
    {
        $ads = $this->ads->pluck('id');
        return $ads->toArray();
    }

    public function getMapViewSettingsProperty()
    {
        return app(MapViewSettings::class);
    }

    /**
     * Update the component based on the filters provided.
     *
     * @param array $filters The set of filters to apply.
     */
    #[On('filter-updated')]
    public function applyFilters($filters)
    {
        $this->filters = array_merge($this->filters, $filters);
        $this->filters = $filters;
        $this->resetPage(); // Reset pagination after filtering.
    }

    #[On('clear-location-filter')]
    public function clearFilters()
    {
        $this->latitude = null;
        $this->longitude = null;
        $this->dispatch('ads-map-updated', ads: $this->ads->pluck('id'));
    }

    /**
     * Update the component based on the field filters provided.
     *
     * @param array $filters The set of filters to apply.
     */
    #[On('field-filter-updated')]
    public function applyFieldFilters($filters)
    {
        $this->fieldFilter = $filters;
        $this->resetPage(); // Reset pagination after filtering.
    }

    #[On('select-filter-updated')]
    public function applyDynamicSelectFilter($filters)
    {
        $this->selectFieldFilter = $filters;
        $this->resetPage(); // Reset pagination after filtering.
    }

    public function getLocationCountProperty()
    {
        $adsGroupedByLocation = $this->ads->groupBy(function ($ad) {
            return $ad->latitude . ',' . $ad->longitude; // Group by latitude and longitude as a single key
        });

        $adsCountByLocation = $adsGroupedByLocation->map(function ($group) {
            return $group->count(); // Count the ads in each group
        });
        return $adsCountByLocation;
    }

    /**
     * Retrieve a list of advertisements based on applied filters.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator The paginated list of ads.
     */
    public function getAdsProperty()
    {

        $query = Ad::query()->when($this->adType,function($query){
            $query->where('ad_type_id', $this->adType?->id);
        })->where('status', 'active');

        // Initiate the query with the active ads.
        // if (is_vehicle_rental_active()) {
        //     $query = \Adfox\VehicleRentalMarketplace\Models\VehicleRentalAd::query()->where('status', 'active');
        // }

        $latitude = session('latitude', null);
        $longitude = session('longitude', null);
        $search_radius = app(LocationSettings::class)->search_radius;
        $radius = $search_radius;
        $selectedCountry = session('country', null);
        $selectedState = session('state', null);
        $selectedCity = session('city', null);
        $locationType = session('locationType', null);


        // Define IDs for Featured and Urgent Promotions
        $featuredPromotionId = 1;
        $urgentPromotionId = 3;

        // Define current date
        $currentDate = now();

        // Create a subquery for featured promotions
        $featuredSubQuery = AdPromotion::selectRaw('COUNT(*)')
            ->whereColumn('ad_id', 'ads.id')
            ->where('promotion_id', $featuredPromotionId)
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->where('active', true);

        // Create a subquery for urgent promotions
        $urgentSubQuery = AdPromotion::selectRaw('COUNT(*)')
            ->whereColumn('ad_id', 'ads.id')
            ->where('promotion_id', $urgentPromotionId)
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->where('active', true);

        // Add select statements for the subqueries and order by them
        $query->addSelect([
            'featured' => $featuredSubQuery,
            'urgent' => $urgentSubQuery,
        ])->orderByDesc('featured')->orderByDesc('urgent');

        if ($this->categorySlug) {
            $category = Category::where('slug', $this->categorySlug)->first();
            // dd($category,$categorySlug,$this->subCategorySlug);
            if ($category) {
                $this->metaTitle = "Ads in {$category->name} - AdFox";
                $this->metaDescription = "Browse the best ads in {$category->name}. Discover amazing deals and offers at AdFox.";

                if (!$this->subCategorySlug) {
                    //Filter main category if main category is doesn't exist filter main category from sub category
                    $query->where(function ($query) use ($category) {
                        $query->where('main_category_id', $category->id)->orWhereHas('category', function ($categoryQuery) use ($category) {
                            $categoryQuery->where('parent_id', $category->id);
                        });
                    });

                } else {
                    // If there's a subcategorySlug provided, use it for filtering
                    $subcategory = Category::where('slug', $this->subCategorySlug)->first();
                    if ($subcategory) {
                        $query->where('category_id', $subcategory->id);
                    }
                }

                //Check if child category exists then fetch ads from child category
                $childCategory = $childCategory = Category::where('slug', $this->childCategorySlug)->first();

                if ($childCategory) {
                    $query->where('child_category_id', $childCategory->id);
                }
            }
        }

        $canFilterLocation = $this->mapViewSettings->map_marker_display_type == 'count' && $this->latitude && $this->longitude;
        //Filter by Latitude longitude
        $query->when($canFilterLocation, function ($query) {
            $query->where('latitude', $this->latitude)->where('longitude', $this->longitude);
        });

        // Conditional location filter based on disable_location
        $query->whereHas('adType', function ($query) use ($locationType, $selectedCountry, $selectedState, $selectedCity, $latitude, $longitude, $radius) {
            $query->where(function ($q) use ($locationType, $selectedCountry, $selectedState, $selectedCity, $latitude, $longitude, $radius) {
                $q->where('disable_location', false)
                    ->where(function ($query) use ($locationType, $selectedCountry, $selectedState, $selectedCity, $latitude, $longitude, $radius) {
                        // Apply location filters based on locationType
                        if ($locationType === 'country' && $selectedCountry) {
                            $query->where('country', $selectedCountry);
                        } elseif ($locationType === 'state' && $selectedState) {
                            $query->where('state', $selectedState);
                        } elseif ($locationType === 'city' && $selectedCity) {
                            $query->where('city', $selectedCity);
                        } elseif ($locationType === 'area' && $latitude && $longitude) {
                            $query->selectRaw("ads.*, (6371 * acos(cos(radians(?))
                      * cos(radians(latitude))
                      * cos(radians(longitude)
                      - radians(?))
                      + sin(radians(?))
                      * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
                                ->having('distance', '<', $radius)
                                ->orderBy('distance', 'ASC');
                        }
                    })
                    ->orWhere('disable_location', true);
            });
        });

        // if ($locationType === 'country' && $selectedCountry) {
        //     $query->where('country', $selectedCountry);
        // } elseif ($locationType === 'state' && $selectedState) {
        //     $query->where('state', $selectedState);
        // } elseif ($locationType === 'city' && $selectedCity) {
        //     $query->where('city', $selectedCity);
        // } elseif ($locationType === 'area' && $latitude && $longitude) {
        //     $query->selectRaw("ads.*, (6371 * acos(cos(radians(?))
        //         * cos(radians(latitude))
        //         * cos(radians(longitude)
        //         - radians(?))
        //         + sin(radians(?))
        //         * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
        //         ->having('distance', '<', $radius)
        //         ->orderBy('distance', 'ASC');
        // }

        $query->when(isset($this->filters['minPrice']) && $this->filters['minPrice'], function ($query) {
            $query->where('price', '>=', $this->filters['minPrice']);
        })
            ->when(isset($this->filters['maxPrice']) && $this->filters['maxPrice'], function ($query) {
                $query->where('price', '<=', $this->filters['maxPrice']);
            })
            ->when(isset($this->filters['brand']) && $this->filters['brand'], function ($query) {
                $query->whereIn('make_id', array_keys($this->filters['brand']));
            })
            ->when(isset($this->filters['fuelType']) && $this->filters['fuelType'], function ($query) {
                $query->whereIn('fuel_type_id', array_keys($this->filters['fuelType']));
            })
            ->when(isset($this->filters['filterTransmission']) && $this->filters['filterTransmission'], function ($query) {
                $query->where('transmission_id', $this->filters['filterTransmission']);
            })
            ->when(isset($this->filters['minMileage']) || isset($this->filters['maxMileage']), function ($query) {
                $minMileage = $this->filters['minMileage'] ?? null;
                $maxMileage = $this->filters['maxMileage'] ?? null;

                if ($minMileage && $maxMileage) {
                    $query->whereBetween('mileage', [$minMileage, $maxMileage]);
                } elseif ($minMileage) {
                    $query->where('mileage', '>=', $minMileage);
                } elseif ($maxMileage) {
                    $query->where('mileage', '<=', $maxMileage);
                }
            })
            ->when(isset($this->filters['startDate']) && $this->filters['startDate'], function ($query) {
                $query->whereDate('start_date', '<=', $this->filters['startDate']);
            })
            ->when(isset($this->filters['endDate']) && $this->filters['endDate'], function ($query) {
                $query->whereDate('end_date', '>=', $this->filters['endDate']);
            })
            ->when(isset($this->filters['features']) && $this->filters['features'], function ($query) {
                $query->whereHas('features', function ($featureQuery) {
                    $featureQuery->whereIn('vehicle_feature_id', array_keys($this->filters['features']));
                });
            });

        // dd($query->get());
        // Add string-based search logic
        if (isset($this->filters['search']) && $this->filters['search']) {
            $searchQuery = $this->filters['search'];

            // Define which columns to search in
            $query->where(function ($query) use ($searchQuery) {
                $query->where('title', 'like', '%' . $searchQuery . '%')  // Search in 'title' column
                    ->orWhere('description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('tags', 'like', '%' . $searchQuery . '%');
            });
        }

        // Sorting logic based on 'sortBy' filter
        if (isset($this->filters['sortBy'])) {
            switch ($this->filters['sortBy']) {
                case 'date':
                    $query->orderBy('posted_date', 'asc'); // For the newest ads first
                    break;
                case 'price_asc':
                    $query->orderBy('price', 'asc'); // For price from Low to High
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc'); // For price from High to Low
                    break;
                case 'date_asc':
                    $query->orderBy('posted_date', 'desc'); // For the newest ads first
                    break;
            }
        }
        // Apply dynamic text field filter
        if (count($this->fieldFilter)) {
            // $fieldKeys=array_keys(Arr::first($this->fieldFilter));
            $fieldValues = \Arr::flatten($this->fieldFilter);
            $query->when(count($fieldValues), function ($query) use ($fieldValues) {
                $query->whereHas('fieldValues', function ($query) use ($fieldValues) {
                    $query->whereIn('id', $fieldValues);
                });
            });
        }

        // Apply dynamic select field filter
        if (count($this->selectFieldFilter)) {
            $filedIds = array_keys($this->selectFieldFilter);
            $fieldValues = \Arr::flatten($this->selectFieldFilter);
            foreach ($this->selectFieldFilter as $field => $value) {
                //run query when field is not empty
                $query->unless(is_null($value), function ($query) use ($field, $value) {

                    $query->whereHas('fieldValues', function ($query) use ($field, $value) {

                        $query->whereHas('field', function ($query) use ($field) {

                            $fieldId = \Arr::last(explode('_', $field));
                            $query->whereId($fieldId);
                        })->where('value', $value);
                    });
                });
            }
        }

        return $query->simplePaginate(25);
    }

    /**
     * Refresh the list of ads when the location is updated.
     */
    #[On('location-updated')]
    public function onLocationUpdated()
    {
        $this->getAdsProperty();
    }

    /**
     * Redirect to location category route when the location is updated.
     */
    #[On('location-redirect')]
    public function onLocationRedirect($locationSlug)
    {
        $slug = slugify($locationSlug);

        $url = "/location/{$slug}";

        $url .= '/' . $this->categorySlug;

        // Append subcategory if available
        if ($this->subCategorySlug) {
            $url .= '/' . $this->subCategorySlug;
        }
        // Perform the redirection
        return redirect($url);
    }

    /**
     * Builds the breadcrumb trail based on the category and subcategory.
     */
    private function buildBreadcrumbs()
    {
        // Start with the home breadcrumb
        $this->breadcrumbs['/'] = __('messages.t_home');

        // Initialize main and subcategories
        $mainCategory = null;
        $subCategory = null;

        if ($this->categorySlug) {
            $mainCategory = Category::where('slug', $this->categorySlug)->first();
        }
        // Add main category breadcrumb
        if(hasMultipleAdTypes() && $this->adType){
            if ($this->categorySlug) {
                    $this->breadcrumbs[route('ad-type.collection', $this->adType?->slug)] = $this->adType?->name;
            } else {
                $this->breadcrumbs[''] = $this->adType?->name;
            }
        }

        // Add subcategory breadcrumb if available
        if ($this->subCategorySlug && $this->adType) {
            $subCategory = Category::where('slug', $this->subCategorySlug)->first();
            $this->breadcrumbs[route('ad-type.collection', $this->adType?->slug) . '?query[categorySlug]=' . $mainCategory?->slug] = $mainCategory?->name;
        }

        // Add child category breadcrumb if available
        $childCategory = Category::where('slug', $this->childCategorySlug)->first();
        if ($childCategory) {
            $this->breadcrumbs[$this->getSubCategoryUrl()] = $subCategory->name;
        }

        // Add breadcrumb for search results or ad listings
        if (isset($this->filters['search']) && $this->filters['search']) {
            $this->breadcrumbs[] = __('messages.t_search_results_for', ['search' => $this->filters['search']]);
        } elseif (isset($childCategory) && $childCategory) {
            $this->breadcrumbs[] = __('messages.t_ad_listings_in', ['category' => $childCategory->name]);
        } elseif (isset($subCategory) && $subCategory) {
            $this->breadcrumbs[] = __('messages.t_ad_listings_in', ['category' => $subCategory->name]);
        } elseif (isset($mainCategory) && $mainCategory) {
            $this->breadcrumbs[] = __('messages.t_ad_listings_in', ['category' => $mainCategory->name]);
        }
    }

    #[On('select-location')]
    public function selectLocation($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        $this->dispatch('ads-map-updated', ads: $this->ads->pluck('id'));
    }

    public function updatedPage()
    {
        $ads = $this->formattedAds['data'] ?? [];
        $this->dispatch('ads-updated', ads: $ads);
    }

    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);

        $separator = $generalSettings->separator ?? '-';
        $title = $generalSettings->site_name ?? app_name();

        $description = $seoSettings->meta_description ?? app_name();
        $ogImage = getSettingMediaUrl('seo.ogimage', 'seo', asset('images/ogimage.jpg')); // Default OG image

        // Determine main category
        $mainCategory = $this->categorySlug ? Category::where('slug', $this->categorySlug)->first() : null;

        // Update OG image if main category is available
        if ($mainCategory) {
            $ogImage = $mainCategory->icon;
        }

        // Determine subcategory
        $subCategory = $this->subCategorySlug ? Category::where('slug', $this->subCategorySlug)->first() : null;

        // Set SEO data based on main or subcategory
        if ($subCategory) {
            $title = $subCategory->name . " $separator " . $title;
            $description = $subCategory->description ?? $description;
        } elseif ($mainCategory) {
            $title = $mainCategory->name . " $separator " . $title;
            $description = $mainCategory->description ?? $description;
        }

        // Update title for search results if search is set
        if (isset($this->filters['search']) && $this->filters['search']) {
            $title = "Search results for " . $this->filters['search'] . " $separator " . $title;
        }

        // Set SEO data
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
        $this->seo()->setCanonical(url()->current());
        $this->seo()->opengraph()->setTitle($title);
        $this->seo()->opengraph()->setDescription($description);
        $this->seo()->opengraph()->setUrl(url()->current());
        $this->seo()->opengraph()->setType('website');
        $this->seo()->opengraph()->addImage($ogImage);
        $this->seo()->twitter()->setImage($ogImage);
        $this->seo()->twitter()->setUrl(url()->current());
        $this->seo()->twitter()->setSite("@" . $seoSettings->twitter_username);
        $this->seo()->twitter()->addValue('card', 'summary_large_image');
        $this->seo()->metatags()->addMeta('fb:page_id', $seoSettings->facebook_page_id, 'property');
        $this->seo()->metatags()->addMeta('fb:app_id', $seoSettings->facebook_app_id, 'property');
        $this->seo()->metatags()->addMeta('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1', 'name');
        $this->seo()->jsonLd()->setTitle($title);
        $this->seo()->jsonLd()->setDescription($description);
        $this->seo()->jsonLd()->setUrl(url()->current());
        $this->seo()->jsonLd()->setType('WebSite');
    }


    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.ad-type.ad-type-collection', ['ads' => $this->ads, 'adsCountByLocation' => $this->locationCount]);
    }
}
