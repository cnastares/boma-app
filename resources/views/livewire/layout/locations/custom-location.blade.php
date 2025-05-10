<div class="flex items-center md:flex-row-reverse justify-end" x-data x-tooltip="{
    content: '{{__('messages.t_tooltip_location')}}',
    theme: $store.theme,
}">
    <button type="button" aria-label="{{__('messages.t_aria_label_location')}}" class="flex items-center md:flex-row-reverse justify-end" @click="$store.location.open = true; document.body.style.overflow = 'hidden'">
        <span class="ml-2 whitespace-nowrap cursor-pointer line-clamp-1 text-right md:text-left" @click="$store.location.open = true; document.body.style.overflow = 'hidden'">{{ $locationName }}</span>
        <x-icon-location class="w-6 h-6 cursor-pointer dark:text-gray-400" @click="$store.location.open = true; document.body.style.overflow = 'hidden'" />
    </button>
    <!-- Modal -->
    <div x-show="$store.location.open" class="fixed inset-0 flex items-start md:pt-20 justify-center z-50 bg-black dark:bg-opacity-90 bg-opacity-50" x-cloak
        >
        <div @click.away="$store.location.open = false; document.body.style.overflow = ''" class="bg-white md:rounded-xl w-[40rem] h-full md:h-auto dark:bg-gray-800 dark:border-white/10 dark:border"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        >
            <!-- Modal Header -->
            <div class="flex justify-between items-center px-6 py-4">
                <h2 class="text-center text-lg md:text-xl">{{ __('messages.t_where_search') }}</h2>
                <button type="button" aria-label="{{__('messages.t_aria_label_close')}}" @click="$store.location.open = false; document.body.style.overflow = ''" class="text-gray-400 hover:text-gray-600">
                    <x-icon-close class="w-4 h-4 md:w-5 md:h-5 classic:text-black" aria-hidden="true"/>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="bg-gray-50 dark:bg-gray-950 px-6 py-6 rounded-b-xl h-full md:h-auto classic:bg-gray-100 ">
                <div>

                    <input id="location-input" name="location" type="text" placeholder="{{ __('messages.t_city') }}" class=" focus-within:ring-2 focus-within:ring-primary-600  focus-within:border-white classic:ring-0 dark:ring-0 w-full px-4 py-3 border border-gray-200 dark:border-white/10 rounded-xl rounded-b-none bg-white  classic:border-black dark:bg-gray-800" wire:model.live.debounce.250ms='locationInput' >

                </div>

                @if ($locationInput)
                    <div class="border-x border-b border-gray-200 dark:border-white/10 rounded-b-xl bg-white classic:border-black dark:bg-gray-800">
                    <!-- Locations from Google -->
                    <div class="px-4  border-gray-200 dark:border-white/10 classic:border-black overflow-y-scroll h-50">
                            @forelse ($customLocationResults as $cityId => $location)
                            <div wire:key='location-result-{{$cityId}}'
                                class="flex items-center my-4 cursor-pointer"
                                role="button"
                                tabindex="0"
                                aria-label="{{__('messages.t_aria_label_select_location')}}"
                                wire:click='selectManualLocation({{$cityId}})'
                                @click="$store.location.open = false; document.body.style.overflow = ''"
                                >
                                <span class="ml-2">{{$location}}</span>
                            </div>
                            @empty
                            <div
                                class="flex items-center my-4 cursor-pointer"
                                >
                                <span class="ml-2">{{__('messages.t_no_results_found')}}</span>
                            </div>
                            @endforelse
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@script
<script>
        let allowedCountries = @json($locationSettings->allowed_countries);

        Alpine.store('location', {
            open: false,
        });

</script>
@endscript
