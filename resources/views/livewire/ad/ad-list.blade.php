<div x-data="{
    show: false,
    showFullScreenMap: false,
    showBottomNavigation: true,
    showMap:false,
    canDisplayLocationCount: @json(app('filament')->hasPlugin('map-view') && $this->mapViewSettings->enable && $this->mapViewSettings->map_marker_display_type == 'count'),
    updateMapContainerHeight(value) {
        const header = document.getElementById('main-header');
        if (value) {
            const headerHeight = document.getElementById('main-header').offsetHeight;
            const elementInterval = setInterval(() => {
                const mapContainer = document.querySelector('.map-container');
                const bottomNavigation = document.querySelector('#bottom-navigation');

                if (mapContainer && header && bottomNavigation) {
                    // If the element exists, stop the interval and execute the callback
                    clearInterval(elementInterval);
                    const computedHeaderStyle = window.getComputedStyle(header);
                    mapContainer.style.height = (document.documentElement.clientHeight - 114 - bottomNavigation.offsetHeight) + 'px';
                    mapContainer.style.top = '1px';
                    document.body.clientHeight = document.documentElement.clientHeight;

                }
            }, 0);
            document.body.scrollTop = document.documentElement.scrollTop = 0;
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
        } else {
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
        }

    },
    init() {
        if (document.documentElement.clientWidth < 1024 && this.canDisplayLocationCount) {
            this.showFullScreenMap = true;
            this.updateMapContainerHeight(true);
        };
    }
}" x-init="$watch('showFullScreenMap', value => {
    updateMapContainerHeight(value);
})

window.addEventListener('resize', () => {
    if (showFullScreenMap) {
        updateMapContainerHeight(true);
    }
});"
    x-on:show-filter.window="show = true;$dispatch('update-sticky-value',false);  document.body.style.overflow = 'hidden';document.body.style.height='100vh'"
    x-on:close-filter.window="show = false;$dispatch('update-sticky-value',true); document.body.style.overflow = 'auto';"
    x-on:close-modal.window="if (showFullScreenMap) {
                updateMapContainerHeight(true);
            }">
    <livewire:layout.header isSearch :$locationSlug :$isMobileHidden />

    <div x-show="!showFullScreenMap" x-cloak>
        @if ($adPlacementSettings->after_header)
            <div class="container mx-auto px-4 flex items-center justify-center md:pt-8 pt-6 " role="complementary" id="header-ad" aria-label="{{ __('messages.t_aria_label_header_advertisement')}}">
                {!! $adPlacementSettings->after_header !!}
            </div>
        @endif
    </div>
    <div
        class="relative {{ (app('filament')->hasPlugin('map-view') && $mapViewSettings->enable && $adPlacementSettings->before_footer) || $adPlacementSettings->before_footer ? ' border-b border-gray-200 classic:border-black dark:border-white/10' : '' }}  ">

        <!-- breadcrumbs -->
        <div x-show="!showFullScreenMap" x-cloak>
            <div class="container mx-auto px-4 my-6">
                <div class="flex justify-between" id="ad-list-breadcrumbs">
                    <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
                    <div class="flex items-center gap-2">
                        <!-- Custom View  -->
                        <div class="md:flex items-center gap-2 hidden">
                            <!-- Grid Button -->
                            <div
                                x-tooltip="{
                                content: '{{ __('messages.t_tooltip_grid_view') }}',
                                theme: $store.theme,
                                }" @click="$wire.set('currentView', 'grid')" aria-label="{{__('messages.t_aria_label_grid_view')}}" class="{{ $currentView == 'grid' ? 'text-primary-600' : 'text-black dark:text-white' }} cursor-pointer">
                                <x-heroicon-o-squares-2x2 class="h-6 w-6" />
                            </div>

                            <!-- List Button -->
                            <div
                                x-tooltip="{
                                content: '{{ __('messages.t_tooltip_list_view') }}',
                                theme: $store.theme,
                                }" @click="$wire.set('currentView', 'list')" aria-label="{{__('messages.t_aria_label_list_view')}}" class="{{ $currentView == 'list' ? 'text-primary-600' : 'text-black dark:text-white' }} cursor-pointer">
                                <x-heroicon-o-queue-list class="h-6 w-6" />
                            </div>

                            @if  (app('filament')->hasPlugin('map-view') && $mapViewSettings->enable && $mapViewSettings->show_map_in_fullscreen)
                                <!-- Map view Button -->
                                <div
                                    x-tooltip="{
                                    content: '{{ __('messages.t_tooltip_map_view') }}',
                                    theme: $store.theme,
                                    }" @click="$wire.set('currentView', 'map');"  aria-label="{{__('messages.t_aria_label_map_view')}}" class="{{ $currentView == 'map' ? 'text-primary-600' : 'text-black dark:text-white' }} cursor-pointer">
                                    <x-heroicon-o-map-pin class="h-6 w-6" />
                                </div>
                            @endif
                        </div>

                        <!-- Filter Button -->
                        @if (app('filament')->hasPlugin('map-view') && $mapViewSettings->enable && $mapViewSettings->show_filter_popup )
                            <button type="button" x-data
                                x-tooltip="{
                                content: '{{ __('messages.t_tooltip_filter') }}',
                                theme: $store.theme,
                            }"
                                class="hidden md:flex bg-white py-1 px-3 rounded-md transition-all dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 classic:ring-black  items-center gap-1"
                                x-on:click="$dispatch('open-modal', { id: 'ad-filter-modal' });">
                                <x-heroicon-o-adjustments-horizontal class="h-5 w-5" />
                                {{ __('messages.t_filter') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content (filter,adlist,map view) -->
        <div class=" grid grid-cols-12 gap-6   {{ app('filament')->hasPlugin('map-view') && $mapViewSettings->enable ? 'md:gap-4 relative  ' : '' }} container mx-auto   items-start {{ app('filament')->hasPlugin('map-view') && $mapViewSettings->enable ? ($mapViewSettings->enable_container_max_width ? '' : 'md:max-w-full md:mx-0 md:pl-2 md:pr-0 ') : '' }} "
            :class="{ 'px-4': !showFullScreenMap }">
            <!-- filter -->
                <div x-trap.noscroll="show"
                    :class="{ 'block fixed left-0 right-0 top-0 bottom-0 z-30': show, 'hidden': !show }"
                    class="col-span-3 {{ !(app('filament')->hasPlugin('map-view') && $mapViewSettings->enable) ? 'md:block' : (!$mapViewSettings->show_filter_popup ? 'md:block md:col-span-3' : '') }}"
                    x-cloak>
                    <livewire:ad.ad-filter :$filters :fieldData="$fieldFilter" :selectFilterData="$selectFieldFilter" :$categorySlug
                        :$subcategorySlug />

                    <!-- Ad placement -->
                    @include('livewire.ad-type._parties.ad-placement')
                </div>

            @if (!($mapViewSettings->show_map_in_fullscreen && $currentView=='map')) <!-- Hidden when showFullScreenMap is true and currentView is map -->
            <div x-show="!showFullScreenMap"
                class=" col-span-12 {{ app('filament')->hasPlugin('map-view') && $mapViewSettings->enable ?
                ($mapViewSettings->show_filter_popup ?
                ($mapViewSettings->show_map_in_fullscreen ? 'md:col-span-12':'md:col-span-7')
                :($mapViewSettings->show_map_in_fullscreen ?'md:col-span-9':'md:col-span-4'))
                : 'md:col-span-9 h-full' }} mb-10  ">

                @if (isset($filters['search']) && $filters['search'])
                    <h1
                        class="text-xl md:text-2xl mb-4 {{ app('filament')->hasPlugin('map-view') && $mapViewSettings->enable ? 'md:hidden' : '' }}">
                        {{ __('messages.t_results_for') }} <span
                            class="font-semibold break-all">{{ $filters['search'] }}</span> </h1>
                @endif

                @if ($adListSettings->sort_by_position=='above_ad_list')
                <!-- Sort by  -->
                <div class="py-6 px-4 flex items-center gap-x-3">
                    <div class="flex items-center gap-x-1">
                        <x-heroicon-o-arrows-up-down class="w-5 h-5" />
                        <x-label for="sort-by" class=" font-medium" value="{{ __('messages.t_sort_by') }} :" />
                    </div>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filters.sortBy" id="sort-by" class="">
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
                @if ($ads->isEmpty())
                    <x-not-found description="{{ __('messages.t_no_ads_for_filter') }}" />
                @else
                <div
                class="{{ $currentView=='grid' ? 'grid-cols-2 sm:grid-cols-4 gap-4 md:gap-4 ' .
                        (app('filament')->hasPlugin('map-view') && $mapViewSettings->enable ?
                        ($mapViewSettings->show_filter_popup ?
                        ($mapViewSettings->show_map_in_fullscreen ? ' md:grid-cols-5 md:gap-3 ' : ' md:grid-cols-3 md:gap-3 ')
                        : ($mapViewSettings->show_map_in_fullscreen ?' md:grid-cols-4 ' : ' md:grid-cols-2 '))
                        : 'lg:grid-cols-4 md:grid-cols-3 ')
                        : ' grid-cols-1 gap-4 ' }} grid">
                        @foreach ($ads as $ad)
                            <livewire:ad.ad-item :$currentView  :ad="$ad" wire:key="search-list-ad-{{ $ad->id }}"
                                lazy />
                        @endforeach
                    </div>
                    <div
                        class="sm:flex-1 sm:flex sm:items-center sm:justify-between mt-10 mb-10 md:mb-0  {{ app('filament')->hasPlugin('map-view') && $mapViewSettings->enable ? 'p-2' : '' }}">
                        <div>
                            <p class="text-base text-slate-700 leading-5 dark:text-slate-400 ">
                                <span>{{ __('messages.t_showing_results') }}</span>
                                <span class="font-medium">{{ ($ads->currentPage() - 1) * $ads->perPage() + 1 }}</span>
                                <span>{{ __('messages.t_to') }}</span>
                                <span
                                    class="font-medium">{{ min($ads->currentPage() * $ads->perPage(), $ads->count()) }}</span>
                                <span>{{ __('messages.t_of') }}</span>
                                <span class="font-medium">{{ $ads->count() }}</span>
                                <span>{{ __('messages.t_results_count') }}</span>

                            </p>
                        </div>
                        <div>
                            {{ $ads->links() }}
                        </div>
                    </div>
                @endif
            </div>
            @endif

            @if (app('filament')->hasPlugin('map-view') && $mapViewSettings->enable && !($mapViewSettings->show_map_in_fullscreen && $currentView!='map'))
                <div wire:ignore.self id="map-component" class=" col-span-12 md:h-[100vh] md:sticky  top-0 md:block
                {{($mapViewSettings->show_filter_popup ?
                ($mapViewSettings->show_map_in_fullscreen && $currentView=='map' ? 'md:col-span-12' : 'md:col-span-5')
                :($mapViewSettings->show_map_in_fullscreen && $currentView=='map' ? 'md:col-span-9' : 'md:col-span-5'))}} "
                    :class="{ 'hidden ': !showFullScreenMap }" >
                    <livewire:map-view :ads="$ads->items()" :$adsCountByLocation  />
                </div>
            @endif
        </div>


        <!-- Map toggle button in mobile view -->
        @if (app('filament')->hasPlugin('map-view') && $mapViewSettings->enable && $this->mapViewSettings->map_marker_display_type !='count')
            <div class="md:hidden   flex justify-center items-center  fixed w-full h-[100px] bottom-12 "
                style="z-index: 2">
                <button type="button"
                    class="flex bg-white py-1 px-3 rounded-md transition-all dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 classic:ring-black  items-center gap-1"
                    x-on:click="showFullScreenMap=!showFullScreenMap">
                    <x-heroicon-o-map-pin aria-hidden="true" x-show="!showFullScreenMap" class="h-5 w-5" x-cloak teleport />
                    <x-heroicon-o-adjustments-horizontal aria-hidden="true" x-show="showFullScreenMap" x-cloak teleport class="h-5 w-5" />
                    <span x-show="!showFullScreenMap" x-cloak teleport> {{ __('messages.t_map_view') }}</span>
                    <span x-show="showFullScreenMap" x-cloak teleport> {{ __('messages.t_list_view') }}</span>
                </button>
            </div>
        @endif
    </div>
    <div x-show="!showFullScreenMap" x-cloak :class="{ 'w-0 h-0': showFullScreenMap }">

        @if ($adPlacementSettings->before_footer)
            <div class="container mx-auto px-4 flex items-center justify-center md:py-10 py-24" role="complementary" aria-label="{{ __('messages.t_aria_label_footer_advertisement')}}">
                {!! $adPlacementSettings->before_footer !!}
            </div>
        @endif
    </div>



    <div x-show="!showFullScreenMap" x-cloak :class="{ 'w-0 h-0': showFullScreenMap }">
        <livewire:layout.footer />
    </div>
    <livewire:layout.bottom-navigation />

    <div :class="{ 'w-0 h-0': showFullScreenMap }">

        <!-- Filter modal  -->
        @if (app('filament')->hasPlugin('map-view') && $mapViewSettings->enable && $mapViewSettings->show_filter_popup)
            <x-filament::modal id="ad-filter-modal" width="xl" x-cloak>
                <x-slot name="heading">
                    {{ __('messages.t_filter') }}
                </x-slot>
                <div>
                    <livewire:ad.ad-filter :$filters :fieldData="$fieldFilter" :selectFilterData="$selectFieldFilter" :$categorySlug
                        :$subcategorySlug />
                </div>
            </x-filament::modal>
        @endif
    </div>
    <div :class="{ '!w-0 !h-0': showFullScreenMap }">
        <livewire:layout.sidebar />
    </div>
</div>
