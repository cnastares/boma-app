<div x-data="{
    show: false,
    showFullScreenMap: false,
    showBottomNavigation: true,
    showMap:false,
    canDisplayLocationCount: @json(mapMarkerDisplayType() == 'count'),
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

    <!-- Skip links -->
    @include('components.skip-links',['links'=>[
    'main-content'=> __('messages.t_skip_to_main_content'),
    'footer'=> __('messages.t_skip_to_footer')
    ]])

    <!-- Header -->
    <livewire:layout.header isSearch :$locationSlug :$isMobileHidden />

    <!-- Ad placement Header -->
    @include('livewire.ad-type._parties.ad-placement-header')

    <!-- Main content -->
    <main id="main-content"
        class="sticky-scroll-margin relative {{ (isMapViewEnabled() && $adPlacementSettings->before_footer) || $adPlacementSettings->before_footer ? ' border-b border-gray-200 classic:border-black dark:border-white/10' : '' }}  ">

        <!-- breadcrumbs -->
        @include('livewire.ad-type._parties.breadcrumbs')

        <!-- Main content -->
        <div class=" grid grid-cols-12 gap-6   {{ isMapViewEnabled() ? 'md:gap-4 relative  ' : '' }} container mx-auto   items-start {{ isMapViewEnabled() ? ($mapViewSettings->enable_container_max_width ? '' : 'md:max-w-full md:mx-0 md:pl-2 md:pr-0 ') : '' }} "
            :class="{ 'px-4': !showFullScreenMap }">

            <!-- filter -->
            @include('livewire.ad-type._parties.filter')

            <!-- Hidden when showFullScreenMap is true and currentView is map -->
            @if (!(isShowMapInFullScreen() && $currentView == 'map'))
            <div x-show="!showFullScreenMap" class=" col-span-12 {{ isMapViewEnabled() ?
                (isMapViewShowFilterPopup() ?
                (isShowMapInFullScreen() ? 'md:col-span-12':'md:col-span-7')
                :(isShowMapInFullScreen() ?'md:col-span-9':'md:col-span-4'))
                : 'md:col-span-9 h-full' }} mb-10  ">

                <h1 class="text-xl md:text-2xl mb-4 {{ isMapViewEnabled() ? 'md:hidden sr-only' : '' }}">
                    {{ __('messages.t_results_for') }} <span class="font-semibold break-all">{{isset($filters['search'])
                        && $filters['search']? $filters['search'] :__('messages.t_ad_list') }}</span>
                </h1>


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

                <!-- Child Categories -->
                @if (count($childCategories) > 0)
                @include('livewire.ad-type.components.category-filter-list')
                @endif

                @if ($ads->isEmpty())
                <x-not-found description="{{ __('messages.t_no_ads_for_filter') }}" />
                @else
                <div class="{{ $currentView=='grid' ? 'grid-cols-2 sm:grid-cols-4 gap-4 md:gap-4 ' .
                        (isMapViewEnabled() ?
                        (isMapViewShowFilterPopup() ?
                        (isShowMapInFullScreen() ? ' md:grid-cols-5 md:gap-3 ' : ' md:grid-cols-3 md:gap-3 ')
                        : (isShowMapInFullScreen() ?' md:grid-cols-4 ' : ' md:grid-cols-2 '))
                        : 'lg:grid-cols-4 md:grid-cols-3 ')
                        : ' grid-cols-1 gap-4 ' }} grid">

                    <!-- Looping Ads list -->
                    @foreach ($ads as $ad)
                    <livewire:ad.ad-item :$currentView :ad="$ad" wire:key="search-list-ad-{{ $ad->id }}" lazy />
                    @endforeach
                </div>

                <!-- Pagination -->
                @include('livewire.ad-type._parties.pagination')

                @endif
            </div>
            @endif

            <!-- Map View -->
            @include('livewire.ad-type._parties.map-view')
        </div>

        <!-- Map toggle button in mobile view -->
        @include('livewire.ad-type._parties.mobile-view-map-toggle-button')
    </main>

    <!-- Ad Placement Footer -->
    @include('livewire.ad-type._parties.ad-placement-footer')

    <!-- Footer Layout -->
    <div x-show="!showFullScreenMap" x-cloak :class="{ 'w-0 h-0': showFullScreenMap }">
        <livewire:layout.footer />
    </div>

    <!-- Bottom Navigation for mobile view -->
    <livewire:layout.bottom-navigation />

    <!-- Ad filter model -->
    @include('livewire.ad-type._parties.modal-ad-fillter')

    <!-- Sidebar -->
    <div :class="{ '!w-0 !h-0': showFullScreenMap }">
        <livewire:layout.sidebar />
    </div>


    <livewire:ad.verify-age :$categorySlug :$subCategorySlug :$childCategorySlug :canRepeat="true" />


</div>
