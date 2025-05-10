<?php

namespace App\Livewire\Ad;

use App\Models\Ad;
use App\Models\AdFieldValue;
use App\Models\Category;
use App\Models\Field;
use App\Settings\MapViewSettings;
use Livewire\Component;

class AdFilter extends Component
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
    public $subcategorySlug = null;
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
    // 'brand' => $this->selectedVehicleMakes,
    /**
     * Mount the component with the given filters.
     *
     * @param array $filters Filters to apply.
     */
    public function mount($filters, $fieldData, $selectFilterData)
    {
        $this->priceRangeMin = Ad::where('status', 'active')->min('price') ?? 0;
        $this->priceRangeMax = Ad::where('status', 'active')->max('price') ?? 0;
        $this->locationSlug = request()->route('location');
        $this->minPrice = $filters['minPrice'] ?? (is_vehicle_rental_active() ? $this->priceRangeMin : null);
        $this->maxPrice = $filters['maxPrice'] ?? (is_vehicle_rental_active() ? $this->priceRangeMax : null);
        $this->sortBy = $filters['sortBy'] ?? 'date';
        $this->minMileage = $filters['minMileage'] ?? null;
        $this->maxMileage = $filters['maxMileage'] ?? null;
        $this->startDate = $filters['startDate'] ?? null;
        $this->endDate = $filters['endDate'] ?? null;

        $this->brand = $filters['brand'] ?? [];
        $this->selectedVehicleMakes = array_map(function ($value) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }, $this->brand);

        $this->fuelType = $filters['fuelType'] ?? [];
        $this->vehicleFuleType = array_map(function ($value) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }, $this->fuelType);

        $this->features = $filters['features'] ?? [];
        $this->vehicleFeatureType = array_map(function ($value) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }, $this->features);

        $this->filterTransmission = $filters['filterTransmission'] ?? null;
        $this->filterData = $fieldData;
        $this->selectFilterData = $selectFilterData;
        // Load main categories
        $this->loadMainCategories();

        // Check and load sub-categories if required
        $this->loadSubCategories();
        $this->loadFilterableField();
        $this->loadSelectableField();
        if (is_vehicle_rental_active()) $this->vehicleRentalSettings();
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
        $this->maxPrice = null;
        $this->minPrice = null;
        $this->minMileage = null;
        $this->maxMileage = null;
        $this->selectedVehicleMakes = [];
        $this->vehicleFuleType = [];
        $this->vehicleFeatureType = [];
        $this->filterTransmission = null;
        $this->startDate = null;
        $this->endDate = null;

        //Clear dynamic filter values
        $this->filterData=[];
        $this->dispatch('field-filter-updated', []);
        $this->selectFilterData=[];
        $this->dispatch('select-filter-updated', []);
    }

    public function loadFilterableField()
    {

        $this->filterableFieldData = collect([]);

        // Get fields that are related to the selected category and their subcategory
        $fields = Field::filterable()
            ->where('type', 'text')
            ->where(function ($query) {
                $query->orWhereHas('categories', function ($query) {
                    $query->where('slug', $this->subcategorySlug);
                })
                ->orWhereHas('fieldTemplates', function ($query) {
                    $query->enabled()->withMainCategoryAndSubCategory($this->categorySlug, $this->subcategorySlug);
                });
            })
            ->with(['adFieldValues' => function ($query) {
                // Get unique values for each field
                $query->select('id', 'field_id', 'value')
                    ->distinct('value')
                    ->whereNotNull('value');
            }, 'adFieldValues.field'])
            ->get();

        $fields->each(function ($fieldData) {
            $uniqueValues = collect();

            $fieldData->adFieldValues->each(function ($field) use ($fieldData, $uniqueValues) {
                // Check if field contain value or field type is select then its not null
                if ($field->value) {
                        //Ensure each value is unique
                        if (!$uniqueValues->contains('value', $field->value)) {
                            $uniqueValues->push([
                                'id' => $field->id,
                                'value' => $field->value,
                                'field' => $field->field
                            ]);
                        }
                }
            });

            if ($uniqueValues->isNotEmpty()) {
                $this->filterableFieldData[$fieldData->name] = $uniqueValues;
            }
        });
    }

    public function loadSelectableField()
    {
        $this->selectableFilters = collect([]);
        //Get the field that is related the Selected category and their subcategory
        $fields = Field::filterable()->where('type', 'select')->whereHas('fieldTemplates', function ($query) {
            $query->enabled()->withMainCategoryAndSubCategory($this->categorySlug, $this->subcategorySlug);
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
        $this->mainCategories = Category::whereNull('parent_id')->get()->sortBy('order');
    }

    /**
     * Check and load sub-categories based on selected category.
     */
    protected function loadSubCategories()
    {
        $this->selectedCategory = Category::where('slug', $this->categorySlug)->first();

        if ($this->selectedCategory) {
            if ($this->subcategorySlug) {
                $selectedSubCategory = Category::where('slug', $this->subcategorySlug)->first();
                $this->isMainCategory = false;
                if ($selectedSubCategory && $selectedSubCategory->parent_id === $this->selectedCategory->id) {
                    $this->subCategories = Category::where('parent_id', $this->selectedCategory->id)->get();
                }
            } else {
                $this->subCategories = Category::where('parent_id', $this->selectedCategory->id)->get();
                $this->isMainCategory = false;
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
        $allowedUpdates = ['minPrice', 'maxPrice', 'sortBy'];
        if (in_array($name, $allowedUpdates)) {
            $data = array_filter([
                'minPrice' => $this->minPrice,
                'maxPrice' => $this->maxPrice,
                'sortBy' => $this->sortBy,
            ]);

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

    /**
     * Select the main category and redirect while keeping the same query parameters.
     *
     * @param string $categorySlug The slug of the main category to select.
     */
    public function selectMainCategory($categorySlug)
    {
        return redirect()->route('ad-category', [
            'category' => $categorySlug,
        ] + request()->query());
    }

    /**
     * Select the sub-category and redirect while keeping the same query parameters.
     *
     * @param string $subcategorySlug The slug of the sub-category to select.
     */
    public function selectSubCategory($categorySlug, $subcategorySlug)
    {
        return redirect()->to(route('ad-category', ['category' => $categorySlug, 'subcategory' => $subcategorySlug]) . '?' . http_build_query(request()->query()));
    }

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
        return view('livewire.ad.ad-filter');
    }
}
