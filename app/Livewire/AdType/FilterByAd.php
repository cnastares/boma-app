<?php

namespace App\Livewire\AdType;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Field;
use App\Settings\MapViewSettings;
use Livewire\Component;

class FilterByAd extends Component
{
    // Properties
    public $minPrice;
    public $maxPrice;
    public $brand = [];
    public $vehicleMakes;
    public ?array $selectedVehicleMakes = [];
    public ?array $vehicleFuleType = [];
    public ?array $vehicleFeatureType = [];
    public $sortBy = 'date';
    public $categorySlug;
    public $subCategorySlug = null;
    public $mainCategories = [];
    public $subCategories = [];
    public $selectedCategory;
    public $isMainCategory = true;
    public $locationSlug;
    public $filterableFieldData;
    public $filterData = [];
    public $selectableFilters = [];
    public $selectFilterData = [];
    public $vehicleTransmissions;
    public $vehicleFule;
    public $vehicleFeature;
    public $filterTransmission;
    public $fuelType = [];
    public $features = [];
    public $minMileage;
    public $maxMileage;
    public $startDate;
    public $endDate;
    public $priceRangeMin;
    public $priceRangeMax;
    public ?array $fieldFilter = [];
    public $adType;

    public function mount($filters, $fieldData, $selectFilterData)
    {
        // Initialize price range for active ads
        $this->priceRangeMin = Ad::active()->min('price') ?? 0;
        $this->priceRangeMax = Ad::active()->max('price') ?? 0;

        // Initialize filters
        $this->initializeFilters($filters);

        // Initialize additional data
        $this->filterData = $fieldData;
        $this->selectFilterData = $selectFilterData;

        // Load categories and filterable fields
        $this->loadMainCategories();
        $this->loadSubCategories();
        $this->loadFilterableDynamicField();
        $this->loadSelectableField();

        // Load vehicle rental settings if active
        if (is_vehicle_rental_active()) {
            $this->vehicleRentalSettings();
        }
    }

    private function initializeFilters($filters)
    {
        $defaultPrice = $this->adType?->filter_settings['enable_price_range'] && $this->adType?->filter_settings['enable_price_range_toggle'] ? $this->priceRangeMin : null;

        $this->sortBy = $filters['sortBy'] ?? 'date';
        $this->minPrice = $filters['minPrice'] ?? $defaultPrice;
        $this->maxPrice = $filters['maxPrice'] ?? $this->priceRangeMax;

        $this->initializeVehicleFilters($filters);
    }

    private function initializeVehicleFilters($filters)
    {
        $this->minMileage = $filters['minMileage'] ?? null;
        $this->maxMileage = $filters['maxMileage'] ?? null;
        $this->startDate = $filters['startDate'] ?? null;
        $this->endDate = $filters['endDate'] ?? null;
        $this->selectedVehicleMakes = $this->validateBooleanArray($filters['brand'] ?? []);
        $this->vehicleFuleType = $this->validateBooleanArray($filters['fuelType'] ?? []);
        $this->vehicleFeatureType = $this->validateBooleanArray($filters['features'] ?? []);
        $this->filterTransmission = $filters['filterTransmission'] ?? null;
    }

    private function validateBooleanArray($array)
    {
        return array_map(fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN), $array);
    }


    public function vehicleRentalSettings()
    {
        $this->vehicleMakes = \Adfox\VehicleRentalMarketplace\Models\VehicleMake::all();
        $this->vehicleTransmissions = \Adfox\VehicleRentalMarketplace\Models\VehicleTransmission::all();
        $this->vehicleFule = \Adfox\VehicleRentalMarketplace\Models\VehicleFuelType::all();
        $this->vehicleFeature = \Adfox\VehicleRentalMarketplace\Models\VehicleFeature::all();
    }
    
    public function clearData()
    {
        $this->maxPrice = $this->minPrice = $this->minMileage = $this->maxMileage = null;
        $this->selectedVehicleMakes = $this->vehicleFuleType = $this->vehicleFeatureType = [];
        $this->filterTransmission = $this->startDate = $this->endDate = null;
    }
    
    public function loadFilterableDynamicField()
    {
        $this->filterableFieldData = collect();
    
        // Fetch filterable fields with relevant conditions
        $fields = Field::filterable()
            ->where('type', 'text')
            ->where(function ($query) {
                $query->orWhereHas('categories', fn($query) => 
                    $query->where('slug', $this->subCategorySlug))
                      ->orWhereHas('fieldTemplates', fn($query) => 
                          $query->enabled()->withMainCategoryAndSubCategory($this->categorySlug, $this->subCategorySlug));
            })
            ->with(['adFieldValues' => function ($query) {
                $query->select('id', 'field_id', 'value')
                    ->distinct('value')
                    ->whereNotNull('value');
            }, 'adFieldValues.field'])
            ->get();
    
        // Process fields and collect unique values
        $fields->each(function ($fieldData) {
            $uniqueValues = $fieldData->adFieldValues
                ->filter(fn($field) => $field->value)
                ->unique('value')
                ->map(fn($field) => [
                    'id' => $field->id,
                    'value' => $field->value,
                    'field' => $field->field,
                ]);
    
            if ($uniqueValues->isNotEmpty()) {
                $this->filterableFieldData[$fieldData->name] = $uniqueValues;
            }
        });
    }
    

    public function backToMainCategory()
    {
        $this->isMainCategory = true;
        $this->filterData['categorySlug'] = null;
    }

    public function loadSelectableField()
    {
        $this->selectableFilters = collect([]);
        //Get the field that is related the Selected category and their subcategory
        $fields = Field::filterable()->where('type', 'select')->whereHas('fieldTemplates', function ($query) {
            $query->enabled()->withMainCategoryAndSubCategory($this->categorySlug, $this->subCategorySlug);
        })->whereHas('adFieldValues', function ($query) {
            return $query->whereNotNull('value');
        })->with('adFieldValues.field')->get();

        $fields->each(function ($fieldData) {
            $fieldData->adFieldValues->each(function ($field) use ($fieldData) {
                if ($field->value || ($field?->field->type->value == 'select' && (!is_null($field->value)))) {
                    if (!$this->selectableFilters->contains($fieldData)) {
                        $this->selectableFilters->push($fieldData);
                    }
                }
            });
        });
    }
    /**
     * Load the main categories.
     */
    protected function loadMainCategories()
    {
        $this->mainCategories = $this->adType?->categories()->whereNull('parent_id')->get()->sortBy('order');
    }

    /**
     * Check and load sub-categories based on selected category.
     */
    protected function loadSubCategories()
    {
        $this->selectedCategory = Category::where('slug', $this->categorySlug)->first();

        if (!$this->selectedCategory) {
            return;
        }

        $this->subCategories = Category::where('parent_id', $this->selectedCategory->id)->get();
        $this->isMainCategory = false;

        if ($this->subCategorySlug) {
            $selectedSubCategory = Category::where('slug', $this->subCategorySlug)->first();
            if (!$selectedSubCategory || $selectedSubCategory->parent_id !== $this->selectedCategory->id) {
                $this->subCategories = collect(); // Clear subcategories if validation fails
            }
        }
    }


    /**
     * Listen for property updates and dispatch filter events accordingly.
     *
     * @param string $name Name of the property that was updated.
     */
    public function updated($name)
    {
        $allowedUpdates = ['minPrice', 'maxPrice', 'sortBy', 'categorySlug', 'subCategorySlug'];
        if (in_array($name, $allowedUpdates)) {
            $data = array_filter([
                'minPrice' => $this->minPrice,
                'maxPrice' => $this->maxPrice,
                'sortBy' => $this->sortBy,
                'categorySlug' => $this->categorySlug,
                'subCategorySlug' => $this->subCategorySlug
            ]);

            $this->loadSubCategories();

            if (!is_vehicle_rental_active()) {
                $this->dispatch('filter-updated', $data);
            }
        }
    }

    public function updatePriceFilter()
    {
        $this->selectedVehicleMakes = array_filter($this->selectedVehicleMakes, function ($value) {
            return $value === true;
        });

        $this->vehicleFuleType = array_filter($this->vehicleFuleType, function ($value) {
            return $value === true;
        });

        $this->vehicleFeatureType = array_filter($this->vehicleFeatureType, function ($value) {
            return $value === true;
        });

        $data = array_filter([
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'sortBy' => $this->sortBy,
            'brand' => $this->selectedVehicleMakes,
            'fuelType' => $this->vehicleFuleType,
            'filterTransmission' => $this->filterTransmission,
            'minMileage' => $this->minMileage,
            'maxMileage' => $this->maxMileage,
            'features' => $this->vehicleFeatureType,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
        // dump($data);
        $this->dispatch('filter-updated', $data);
        $this->dispatch('update-range');

        if (app('filament')->hasPlugin('map-view') && $this->mapViewSettings->enable) {
            $this->dispatch('close-modal', id: 'ad-filter-modal');
        }
    }

    // /**
    //  * Select the main category and redirect while keeping the same query parameters.
    //  *
    //  * @param string $categorySlug The slug of the main category to select.
    //  */
    // public function selectMainCategory($categorySlug)
    // {
    //     return redirect()->route('ad-category', [
    //         'category' => $categorySlug,
    //     ] + request()->query());
    // }

    // /**
    //  * Select the sub-category and redirect while keeping the same query parameters.
    //  *
    //  * @param string $subCategorySlug The slug of the sub-category to select.
    //  */
    // public function selectSubCategory($categorySlug, $subCategorySlug)
    // {
    //     return redirect()->to(route('ad-category', ['category' => $categorySlug, 'subcategory' => $subCategorySlug]) . '?' . http_build_query(request()->query()));
    // }

    /**
     * Apply filters
     * @return void
     */
    public function applyFilters()
    {
        $filterData = collect($this->filterData)
            ->map(function ($values) {
                // Convert associative array of checked values to array of IDs
                return collect($values)
                    ->filter(function ($checked, $id) {
                        return $checked;
                    })
                    ->keys()
                    ->toArray();
            })
            // Remove empty arrays
            ->filter(function ($values) {
                return !empty($values);
            })
            ->toArray();
        // Update price filter
        $this->updatePriceFilter();
        //Send dispatch to AdList Page
        $this->dispatch('field-filter-updated', $filterData);
    }

    public function updatedSelectFilterData()
    {
        // Remove filter
        $filter = \Arr::where($this->selectFilterData, function (string|int $value, string $key) {
            if ($value == 'null') {
                unset($this->selectFilterData[$key]);
            }
        });
        $this->dispatch('select-filter-updated', $this->selectFilterData);
    }

    public function getMapViewSettingsProperty()
    {
        return app(MapViewSettings::class);
    }
    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.ad-type.filter-by-ad');
    }
}
