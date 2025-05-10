<div x-show="!showFullScreenMap" x-cloak >
    <div class="container overflow-auto mx-auto px-4 py-6">
        <div class="flex justify-between" id="ad-list-breadcrumbs">
            <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
            <div class="flex items-center gap-2">
                <!-- Custom View  -->
                <div class="md:flex items-center gap-2 hidden">
                    <!-- Grid Button -->
                    <button type="button" aria-label="{{__('messages.t_aria_label_grid_view')}}" x-tooltip="{
                                content: '{{ __('messages.t_tooltip_grid_view') }}',
                                theme: $store.theme,
                                }" @click="$wire.set('currentView', 'grid')" class="{{ $currentView == 'grid' ? 'text-primary-600' : 'text-black dark:text-white' }} cursor-pointer">
                        <x-heroicon-o-squares-2x2 class="h-6 w-6" aria-hidden="true" />
                    </button>

                    <!-- List Button -->
                    <button type="button" aria-label="{{__('messages.t_aria_label_list_view')}}" x-tooltip="{
                                content: '{{ __('messages.t_tooltip_list_view') }}',
                                theme: $store.theme,
                                }" @click="$wire.set('currentView', 'list')" class="{{ $currentView == 'list' ? 'text-primary-600' : 'text-black dark:text-white' }} cursor-pointer">
                        <x-heroicon-o-queue-list class="h-6 w-6" aria-hidden="true" />
                    </button>

                    @if (isMapViewEnabled() && isShowMapInFullScreen())
                    <!-- Map view Button -->
                    <button type="button" aria-label="{{__('messages.t_aria_label_map_view')}}" x-tooltip="{
                                    content: '{{ __('messages.t_tooltip_map_view') }}',
                                    theme: $store.theme,
                                    }" @click="$wire.set('currentView', 'map');" class="{{ $currentView == 'map' ? 'text-primary-600' : 'text-black dark:text-white' }} cursor-pointer">
                        <x-heroicon-o-map-pin class="h-6 w-6" aria-hidden="true" />
                    </button>
                    @endif
                </div>

            @if ($adType?->enable_filters)
                <!-- Filter Button -->
                @if (getMapViewSetting('enable') && isMapViewShowFilterPopup())
                <button type="button" x-data
                    x-tooltip="{
                                content: '{{ __('messages.t_tooltip_filter') }}',
                                theme: $store.theme,
                            }"
                    class="hidden md:flex bg-white py-1 px-3 rounded-md transition-all dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 classic:ring-black  items-center gap-1"
                    x-on:click="document.body.scrollTop = document.documentElement.scrollTop = 0;$dispatch('open-modal', { id: 'ad-filter-modal' });">
                    <x-heroicon-o-adjustments-horizontal class="h-5 w-5" />
                    {{ __('messages.t_filter') }}
                </button>
                @endif
            @endif
            </div>
        </div>
    </div>
</div>