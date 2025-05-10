<div class=" bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:rounded-xl flex flex-col classic:ring-black h-full pb-5 md:pb-0 mb-5"
    x-data="{
        minPrice: $wire.entangle('minPrice'),
        maxPrice: $wire.entangle('maxPrice'),
    }">
    <style>
        /* Hide scrollbar for all browsers */
        .hide-scrollbar {
            overflow: auto;
            /* or overflow: scroll; */
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* Internet Explorer 10+ */
        }

        /* Hide scrollbar for WebKit browsers (Chrome, Safari) */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
            /* WebKit */
        }
    </style>
    @if (is_vehicle_rental_active())
        <div class="flex justify-between items-center py-3 px-4 border-b border-black mb-4 md:hidden">
            <h3 class="text-lg">{{ __('messages.t_filter') }} </h3>
            <div class="cursor-pointer"
                x-on:click="$dispatch('close-filter');document.documentElement.style.overflow = 'auto'; document.body.style.overflow = '';">
                <x-icon-close class="w-4 h-4" />
            </div>
        </div>
    @endif
    <div
        class="flex-grow overflow-y-auto hide-scrollbar divide-y divide-gray-200 dark:divide-white/10 classic:divide-black">
        <!-- Category List -->
        {{-- @if (app('currentBusinessType') == 'general') --}}
        <div x-data="{ queryString: window.location.search, startWatching() { this.interval = setInterval(() => { if (this.queryString !== window.location.search) { this.queryString = window.location.search } }, 100); }, interval: null }" x-init="startWatching()" class="py-6 px-4">

            <div class=" flex justify-between">
                <h3 class="mb-2 font-medium">{{ __('messages.t_categories') }}</h3>
                @if (!is_vehicle_rental_active())
                    <div class=" md:hidden cursor-pointer"
                        x-on:click="$dispatch('close-filter');document.documentElement.style.overflow = 'auto'; document.body.style.overflow = '';">
                        <x-icon-close class="w-4 h-4" />
                    </div>
                @endif
            </div>
            <ul class="overflow-y-auto max-h-96">
                @if ($isMainCategory)
                    @foreach ($mainCategories as $category)
                        <li wire:key='filter-main-{{ $category->slug }}' class="mb-1 cursor-pointer">
                            <a :href="'{{ url(generate_category_url($category->adType, $category, null, $locationSlug)) }}' + (queryString ?
                                queryString : '')"
                                wire:navigate>
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                @else
                    <!-- Display selected category's parent at the top with a back arrow -->
                    <li class="font-medium mb-1 cursor-pointer flex items-center">
                        <x-heroicon-o-arrow-left role="button" wire:click="$set('isMainCategory', true)"
                            class="w-5 h-5 mr-1 rtl:scale-x-[-1] " />
                        <a :href="'{{ url(generate_category_url($category->adType, $selectedCategory->parent ? $selectedCategory->parent : $selectedCategory, null, $locationSlug)) }}' +
                        (queryString ? queryString : '')"
                            wire:navigate>
                            {{ $selectedCategory->parent ? $selectedCategory->parent->name : $selectedCategory->name }}
                        </a>
                    </li>
                    @foreach ($subCategories as $subCategory)
                        <li wire:key='filter-sub-{{ $subCategory->slug }}'
                            class="{{ $subCategory->slug == $subcategorySlug ? 'underline' : '' }} mb-1 pl-10 cursor-pointer">
                            <a :href="'{{ url(generate_category_url($category->adType, $selectedCategory, $subCategory, $locationSlug)) }}' + (
                                queryString ? queryString : '')"
                                wire:navigate>
                                {{ $subCategory->name }}
                            </a>
                        </li>
                    @endforeach

                @endif
            </ul>
        </div>
        {{-- @endif --}}

        @if (!is_vehicle_rental_active() && $adListSettings->sort_by_position=='filter_box')
            <!-- Sort By -->
            <div class="py-6 px-4">
                <x-label for="sort-by" class="mb-2 font-medium" value="{{ __('messages.t_sort_by') }}" />
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="sortBy" id="sort-by">
                        <option value="date">{{ __('messages.t_date') }}</option>
                        <option value="price_asc">{{ __('messages.t_price_low_to_high') }}</option>
                        <option value="price_desc">{{ __('messages.t_price_high_to_low') }}</option>
                        @if (is_ecommerce_active())
                            <option value="date_asc">{{ __('messages.t_latest') }}</option>
                        @endif

                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        @endif


        {{-- <div class="py-4">
            <!-- Min Price -->
            <div class="py-2 px-4">
                <x-label class="mb-2 font-medium" for="min-price" value="{{ __('messages.t_min_price') }}" />
        <x-filament::input.wrapper>
            <x-filament::input :placeholder="__('messages.t_enter_min_price')" type="text" wire:model.blur="minPrice" id="min-price" />
        </x-filament::input.wrapper>
    </div>

    <!-- Max Price -->
    <div class="py-2 px-4">
        <x-label class="mb-2 font-medium" for="max-price" value="{{ __('messages.t_max_price') }}" />
        <x-filament::input.wrapper>
            <x-filament::input :placeholder="__('messages.t_enter_max_price')" type="text" wire:model.blur="maxPrice" id="max-price" />
        </x-filament::input.wrapper>

    </div>
</div> --}}

        @if (!is_vehicle_rental_active())
            <div class="p-4 gap-2 flex justify-between items-center">
                <!-- Min Price -->
                <div class="">
                    <x-label class="mb-2 font-medium" for="min-price" value="{{ __('messages.t_min_price') }}" />
                    <x-filament::input.wrapper>
                        <x-filament::input :placeholder="__('messages.t_min_price_placeholder')" type="text" x-model.live.debounce.500="minPrice"
                            wire:model="minPrice" id="min-price" x-mask="9999999999999999" />
                    </x-filament::input.wrapper>
                </div>

                <!-- Max Price -->
                <div class="">
                    <x-label class="mb-2 font-medium" for="max-price" value="{{ __('messages.t_max_price') }}" />
                    <x-filament::input.wrapper>
                        <x-filament::input :placeholder="__('messages.t_max_price_placeholder')" type="text" x-model.live.debounce.500="maxPrice"
                            wire:model="maxPrice" id="max-price" x-mask="9999999999999999" />
                    </x-filament::input.wrapper>
                </div>
                {{-- <button class="mt-6 cursor-pointer disabled:cursor-not-allowed disabled:text-gray-500 text-primary-500"
                    wire:click='updatePriceFilter' x-bind:disabled="(minPrice || maxPrice) ? false: true" x-data
                    x-tooltip="{
                        content: '{{ __('messages.t_tooltip_search') }}',
                        theme: $store.theme,
                    }">
                    <x-heroicon-s-magnifying-glass-circle class="  h-10 w-10 -ml-[6px]" />
                </button> --}}
            </div>
        @endif

        @if (is_vehicle_rental_active())
            <div>
                <div class="range p-4">
                    <div style="width: 97%;">
                        <div class="range-slider">
                            <span class="range-selected bg-[black] dark:bg-primary-600"></span>
                        </div>
                        @php
                            $min = $this->priceRangeMin;
                            $max = $this->priceRangeMax;
                        @endphp
                        <div class="range-input pb-2">
                            <input type="range" role="slider" class="min" wire:model="minPrice" min="{{ $min }}"
                                max="{{ $max }}" value="{{ $minPrice }}" step="10" wire:ignore name="minPrice" aria-label="{{__('messages.t_aria_label_min_price') }}">
                            <input type="range" role="slider" class="max" wire:model="maxPrice" min="{{ $min }}"
                                max="{{ $max }}" value="{{ $maxPrice }}" step="10" wire:ignore name="maxPrice" aria-label="{{__('messages.t_aria_label_max_price') }}">
                        </div>
                    </div>
                    <div class="range-price">
                        <div class="w-full">
                            <x-label class="mb-2 font-medium" for="min-price"
                                value="{{ __('messages.t_min_price') }}" />
                            <x-filament::input.wrapper>
                                <x-filament::input placeholder="eg 100" type="text" class="w-full !px-3 font-light"
                                    wire:model="minPrice" id="min-price" x-mask="9999999999999999" :disabled="true" />
                            </x-filament::input.wrapper>
                        </div>
                        {{-- <x-heroicon-o-arrow-right class="text-gray-500 h-4 w-5" /> --}}
                        <div class="w-full">
                            <x-label class="mb-2 font-medium" for="max-price"
                                value="{{ __('messages.t_max_price') }}" />
                            <x-filament::input.wrapper>
                                <x-filament::input placeholder="eg 2000" type="text" class="w-full !px-3 font-light"
                                    wire:model="maxPrice" id="max-price" x-mask="9999999999999999" :disabled="true" />
                            </x-filament::input.wrapper>
                        </div>
                    </div>
                </div>

            </div>
        @endif
        @if ($filterableFieldData && $filterableFieldData->isNotEmpty())
        <div class="border-t border-gray-200 dark:border-white/10 px-4 py-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.t_dynamic_filters') }}</h3>

            @foreach ($filterableFieldData as $fieldName => $fieldValues)
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $fieldName }}</h4>
                    <div class="mt-2 space-y-2">
                        @foreach ($fieldValues as $index => $fieldValue)
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="field-{{ $fieldName }}-{{ $index }}"
                                    id="field-value-{{ $fieldName }}-{{ $index }}"
                                    wire:model.live="filterData.{{ $fieldName }}.{{ $fieldValue['id'] }}"
                                    class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-800 dark:border-white/10 dark:text-white dark:focus:ring-primary-500"
                                >
                                <label for="field-value-{{ $fieldName }}-{{ $index }}" class="ml-3 text-sm text-gray-600 dark:text-white hover:cursor-pointer">
                                    {{ is_array($fieldValue['value']) ? implode(', ', $fieldValue['value']) : $fieldValue['value'] }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

        @if (count($selectableFilters))
            <div class=" py-4">
                @foreach ($selectableFilters as $field)
                    <div class="py-2 px-4" wire:key='select-field-{{ $field->id }}'>
                        <x-label class="mb-2 font-medium" for="min-price" value="{{ $field->name }}" />
                        <x-filament::input.wrapper>
                            <x-filament::input.select
                                wire:model="selectFilterData.{{ $field->name . '_' . $field->id }}">
                                <option value="null">Select Filter</option>
                                @foreach ($field->options as $index => $value)
                                    <option value="{{ $index }}"
                                        wire:key='select-value-{{ $index . ' -' . $value }}'>{{ $value }}
                                    </option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>
                @endforeach
            </div>
        @endif


        @if (is_vehicle_rental_active())
            <div class="px-2 py-3 overflow-x-auto">

                <!-- start Date -->
                <h3 class="px-2 font-medium">Date</h3>
                <div class="pb-4 pt-2 px-2 gap-2 flex items-center justify-between w-full">
                    <div class="w-full">
                        <style>
                            @media (max-width: 678px) {
                                .date-filter {
                                    position: relative;
                                    width: 100%;
                                }

                                .date-filter:before {
                                    color: lightgray;
                                    position: absolute;
                                    content: attr(placeholder);
                                    padding-left: 10px;
                                    left: 0;
                                }
                            }
                        </style>
                        <x-filament::input.wrapper>
                            <x-filament::input class=" date-filter dark:!text-gray-500" type="date"
                                wire:model="startDate" placeholder="{{ !$startDate ? 'dd/mm/yyyy' : '' }}" />
                        </x-filament::input.wrapper>
                    </div>
                    <x-heroicon-o-arrow-right class="text-gray-500 h-4 w-5" />
                    <!-- end Date -->
                    <div class="w-full">
                        <x-filament::input.wrapper>
                            <x-filament::input type="date" class="date-filter dark:!text-gray-500"
                                wire:model="endDate" placeholder="{{ !$endDate ? 'dd/mm/yyyy' : '' }}" />
                        </x-filament::input.wrapper>
                    </div>
                </div>
            </div>
        @endif



        @if (is_vehicle_rental_active())
            <div class="p-2">
                <!-- Brand -->
                <h3 class="px-2 font-medium">Brand</h3>
                <div class="pb-4 pt-2 px-2 gap-[10px] flex flex-wrap">
                    <!-- Brand checkbox -->
                    @foreach ($vehicleMakes as $type)
                        <label style="word-spacing: 4px" wire:key={{ $type->id }}>
                            <x-filament::input.checkbox wire:model="selectedVehicleMakes.{{ $type->id }}"
                                style="height: 18px; width: 18px;" :checked="in_array($type->id, $brand ?? [])" />
                            {{-- --}}
                            <span class="text-sm font-normal">
                                {{ $type->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        @if (is_vehicle_rental_active())
            <div class="p-2">
                <!-- Transmission -->
                <h3 class="px-2 font-medium">{{ __('messages.t_transmission') }}</h3>
                <div class="pb-4 pt-2 px-2">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model="filterTransmission" color="info"
                            class=" dark:!text-gray-500">
                            <option value="{{ null }}" class=" dark:!text-gray-500">All</option>
                            @foreach ($vehicleTransmissions as $type)
                                <option value="{{ $type->id }}" class=" dark:!text-gray-500">
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
        @endif

        @if (is_vehicle_rental_active())
            <div class="p-2">
                <!-- vehicleFule-->
                <h3 class="px-2 font-medium">{{ __('messages.t_fuel') }}</h3>
                <div class="pb-4 pt-2 px-2 gap-[15px] flex flex-wrap">
                    <!-- Brand radio -->
                    @foreach ($vehicleFule as $type)
                        <label style="word-spacing: 4px">
                            <x-filament::input.checkbox wire:model="vehicleFuleType.{{ $type->id }}"
                                style="height: 18px; width: 18px;" />
                            <span class="text-sm font-normal">
                                {{ $type->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- @if (is_vehicle_rental_active())
            <div class="p-2">
                <!-- Min Mileage -->updateRange();
                <h3 class="px-2 font-medium">Mileage</h3>
                <div class="pb-4 pt-2 px-2 gap-2 flex items-center ">
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input placeholder="Minimum" type="text" wire:model="minMileage" />
                        </x-filament::input.wrapper>
                    </div>
                    <span>-</span>
                    <!-- Max Mileage -->
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input placeholder="Maximum" type="text" wire:model="maxMileage" />
                        </x-filament::input.wrapper>
                    </div>
                </div>
            </div>
        @endif --}}

        @if (is_vehicle_rental_active())
            <div class="p-2">
                <!-- Features -->
                <h3 class="px-2 font-medium">{{ __('messages.t_vehicle_features') }}</h3>
                <div class="pb-4 pt-2 px-2 gap-[15px] flex flex-wrap">
                    <!-- Brand radio -->
                    @foreach ($vehicleFeature as $type)
                        <label style="word-spacing: 4px" wire:key="features-" .{{ $type->id }}>
                            <x-filament::input.checkbox wire:model="vehicleFeatureType.{{ $type->id }}"
                                style="height: 18px; width: 18px;" />
                            <span class="text-sm font-normal">
                                {{ $type->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif


        @if (!is_vehicle_rental_active())
        <div class="py-2 px-4 md:block hidden cusor-pointer">
            <x-button.primary
                x-on:click="$dispatch('close-filter');document.documentElement.style.overflow = ''; document.body.style.overflow = '';"
                wire:click='applyFilters'
                class="w-full dark:!bg-primary-600">{{ __('messages.t_apply') }}</x-button.primary>
        </div>
        @endif

        @if (is_vehicle_rental_active() &&
                !(app('filament')->hasPlugin('map-view') && $mapViewSettings->enable && $mapViewSettings->show_filter_popup))
            <div class="p-2 justify-between items-center hidden lg:flex">
                <!-- Min Price -->
                <h3 class="px-2 font-medium cursor-pointer text-base" wire:click="clearData">
                    {{ __('messages.t_clear_all') }} </h3>
                <div class="pb-4 pt-2 px-2 gap-2">
                    <x-filament::button wire:click='applyFilters'
                        class="!bg-primary-600 !text-black classic:border classic:border-black">
                        {{ __('messages.t_search') }}
                    </x-filament::button>
                </div>
            </div>
        @endif

    </div>
    @if (is_vehicle_rental_active() &&
            (app('filament')->hasPlugin('map-view') && $mapViewSettings->enable && $mapViewSettings->show_filter_popup))
        <div class="p-2 md:flex justify-between items-center hidden ">
            <!-- Min Price -->
            <h3 class="px-2 font-medium cursor-pointer text-base" wire:click="clearData">
                {{ __('messages.t_clear_all') }} </h3>
            <div class="pb-4 pt-2 px-2 gap-2">
                <x-filament::button wire:click='applyFilters'
                    class="!bg-primary-600 !text-black classic:border classic:border-black">
                    {{ __('messages.t_search') }}
                </x-filament::button>
            </div>
        </div>
    @endif

    @if (is_vehicle_rental_active())
        <div class="p-2 flex justify-between items-center md:hidden">
            <!-- Min Price -->
            <h3 class="px-2 font-medium cursor-pointer text-base" wire:click="clearData">
                {{ __('messages.t_clear_all') }} </h3>
            <div class="pb-4 pt-2 px-2 gap-2">
                <x-filament::button wire:click='applyFilters'
                    class="!bg-primary-600 !text-black classic:border classic:border-black"
                    x-on:click="$dispatch('close-filter');document.documentElement.style.overflow = ''; document.body.style.overflow = '';">
                    {{ __('messages.t_search') }}
                </x-filament::button>
            </div>
        </div>
    @endif

    @if (!is_vehicle_rental_active())
        <div class="py-2 px-4 md:hidden cusor-pointer">
            <x-button.secondary
                x-on:click="$dispatch('close-filter');document.documentElement.style.overflow = ''; document.body.style.overflow = '';"
                wire:click='applyFilters'
                class="w-full dark:!bg-primary-600">{{ __('messages.t_see_filtered_results') }}</x-button.secondary>
        </div>
    @endif
    <style>
        .range-slider {
            height: 5px;
            position: relative;
            background-color: #e1e9f6;
            border-radius: 2px;
            /* width: 97%; */
        }

        .range-selected {
            height: 100%;
            position: absolute;
            border-radius: 5px;
            /* background-color: #1b53c0; */
            left: 0;
            right: 0;
            transition: left 0.1s ease, right 0.1s ease;
            /* Smooth transitions */
        }

        .range-input {
            position: relative;
        }

        .range-input input {
            position: absolute;
            width: 103%;
            height: 5px;
            top: -7px;
            background: none;
            pointer-events: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        :root {
            --thumb-border-color: #000000;
        }

        .dark {
            --thumb-border-color: var(--primary);
        }

        .range-input input[type="range"]::-webkit-slider-thumb {
            height: 20px;
            width: 20px;
            border-radius: 50%;
            border: 3px solid var(--thumb-border-color);
            background-color: #fff;
            pointer-events: auto;
            -webkit-appearance: none;
        }

        .range-input input[type="range"]::-moz-range-thumb {
            height: 20px;
            width: 20px;
            border-radius: 50%;
            border: 3px solid var(--thumb-border-color);
            background-color: #fff;
            pointer-events: auto;
        }

        .range-price {
            margin: 10px 0;
            width: 100%;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .range-price label {
            margin-right: 5px;
        }

        .range-price input {
            width: 100px;
            padding: 5px;
        }

        .range-price input:first-of-type {
            margin-right: 15px;
        }
    </style>

    @if (is_vehicle_rental_active())
        @script
            <script>
                const range = document.querySelector(".range-selected");

                const rangeInput = document.querySelectorAll(".range-input input");
                const rangePrice = document.querySelectorAll(".range-price input");

                const minRangeGap = 10; // Minimum gap between the two ranges

                function updateRange() {
                    const minValue = parseInt(rangeInput[0].value);
                    const maxValue = parseInt(rangeInput[1].value);
                    // console.log(maxValue);
                    // Ensure minimum gap
                    if (maxValue - minValue < minRangeGap) {
                        if (event.target.classList.contains("min")) {
                            rangeInput[0].value = maxValue - minRangeGap;
                        } else {
                            rangeInput[1].value = minValue + minRangeGap;
                        }
                    }

                    // Update price input fields
                    rangePrice[0].value = rangeInput[0].value;
                    rangePrice[1].value = rangeInput[1].value;

                    // Calculate percentages for styling
                    const minPercentage = ((minValue - rangeInput[0].min) / (rangeInput[0].max - rangeInput[0].min)) * 100;
                    const maxPercentage = ((maxValue - rangeInput[1].min) / (rangeInput[1].max - rangeInput[1].min)) * 100;

                    // Update the selected range style
                    range.style.left = `${minPercentage}%`;
                    range.style.right = `${100 - maxPercentage}%`;
                }

                // Add event listeners to the range inputs
                rangeInput.forEach(input => {
                    input.addEventListener("input", updateRange);
                });

                // Add event listeners for the price inputs
                rangePrice.forEach((input, index) => {
                    input.addEventListener("input", () => {
                        const minPrice = parseInt(rangePrice[0].value);
                        const maxPrice = parseInt(rangePrice[1].value);

                        if (maxPrice - minPrice >= minRangeGap && maxPrice <= rangeInput[1].max && minPrice >=
                            rangeInput[0].min) {
                            if (index === 0) {
                                rangeInput[0].value = minPrice;
                            } else {
                                rangeInput[1].value = maxPrice;
                            }
                            updateRange();
                        }
                    });
                });

                $wire.on('update-range', () => {
                    const range = document.querySelector(".range-selected");
                    const rangeInput = document.querySelectorAll(".range-input input");
                    const rangePrice = document.querySelectorAll(".range-price input");

                    if (!range || rangeInput.length < 2 || rangePrice.length < 2) {
                        console.error("Elements not found in the DOM.");
                        return;
                    }

                    const minValue = parseInt(rangeInput[0].value);
                    const maxValue = parseInt(rangeInput[1].value);

                    if (maxValue - minValue < minRangeGap) {
                        if (event.target.classList.contains("min")) {
                            rangeInput[0].value = maxValue - minRangeGap;
                        } else {
                            rangeInput[1].value = minValue + minRangeGap;
                        }
                    }

                    rangePrice[0].value = rangeInput[0].value;
                    rangePrice[1].value = rangeInput[1].value;

                    const minPercentage = ((minValue - rangeInput[0].min) / (rangeInput[0].max - rangeInput[0].min)) * 100;
                    const maxPercentage = ((maxValue - rangeInput[1].min) / (rangeInput[1].max - rangeInput[1].min)) * 100;

                    // Apply styles with !important
                    setTimeout(() => {
                        range.style.setProperty('left', `${minPercentage}%`, 'important');
                        range.style.setProperty('right', `${100 - maxPercentage}%`, 'important');
                    }, 10);

                    // console.log(range, "Range styles updated:", range.style.left, range.style.right);
                });


                // Initialize the range on page load
                updateRange();
            </script>
        @endscript
    @endif

</div>
