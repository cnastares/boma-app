<div  class='mt-4'>
    <p class="mb-4">{{ __('messages.t_confirm_location') }}</p>


    <x-input-error :messages="$errors->get('locationName')" class="mb-4" />

    <div class="rounded-b" x-data>

        <div class="border mb-6 border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-white/10 classic:border-black">
            <!-- Current Location Section -->
            <button aria-label="{{__('messages.t_aria_label_use_current_location')}}" type="button"  @click="$store.postad.getLocation($wire)" class="flex items-center  gap-x-2 px-4 py-2 cursor-pointer">
                <x-icon-location class="w-5 h-5"/>
                <div class="text-left">
                    <span>
                        {{ __('messages.t_use_current_location') }}
                    </span>
                    <div class="text-gray-600 text-base">
                        @if ($this->city && $this->state && $this->country)
                           {{$this->city}} - {{$this->state}}
                        @endif
                    </div>
                    <div x-data="{ locationFetch: @entangle('locationFetch') }" class="text-green-600" x-show="locationFetch">{{  __('messages.t_locating_you') }}</div>

                    <div x-data="{ locationBlocked: @entangle('locationBlocked') }" class="text-danger-600" x-show="locationBlocked">{{ __('messages.t_location_blocked') }}</div>
                </div>
            </button>
            <!-- Locations from Google -->
            <div class="px-4 border-t border-gray-200  dark:border-white/10 classic:border-black" x-show="$store.postad.locations && $store.postad.locations.length > 0">
                <template x-for="(location, index) in $store.postad.locations" :key="index">
                    <div @click="$store.postad.selectLocation(location, $wire, 'post-ad')" class="flex items-center my-4 cursor-pointer" >
                        <span class="ml-2" x-text="location.description"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <div class="flex items-center justify-center my-6">
        <div class="flex-grow border-t border-gray-200  dark:border-white/10 classic:border-black"></div>
        <span class="mx-4">{{ __('messages.t_or') }}</span>
        <div class="flex-grow border-t border-gray-200  dark:border-white/10 classic:border-black"></div>
    </div>

    <div class="mb-4">
        <p class="mb-4"> {{ __('messages.t_prefer_manually') }} </p>
        @if(!$locationFetch)
            <form wire:submit.prevent="create">
                {{ $this->form }}
            </form>
        @endif
    </div>
    <x-filament-actions::modals />

</div>


@script
<script>
        Alpine.store('postad', {
            open: false,
            locations: [],
            getLocation(wire) {
                wire.set('locationFetch', true);
                navigator.geolocation.getCurrentPosition(position => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    const googleLocationStatus = @json($this->googleSettings?->status);
                    const hasGoogleLocationKit = @json(app('filament')->hasPlugin('google-location-kit'));

                    if (hasGoogleLocationKit && googleLocationStatus) {
                        this.fetchLocationFromGoogle(latitude, longitude, wire);
                    } else {
                        this.fetchLocationFromNominatim(latitude, longitude, wire);
                    }

                }, () => {
                    wire.set('locationBlocked', true);
                    wire.set('locationFetch', false);
                });
            },
            fetchLocationFromGoogle(latitude, longitude, wire) {

                const GOOGLE_API_KEY = "{{ $this->googleSettings?->api_key }}";
                // Call the geocoding API to fetch the location name
                fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key=${GOOGLE_API_KEY}`)
                .then(response => response.json())
                .then(data => {
                    if (data.results && data.results.length > 0) {
                        const addressComponents = data.results[0].address_components;
                        const city = addressComponents.find(comp => comp.types.includes('locality'))?.short_name ;
                        const state = addressComponents.find(comp => comp.types.includes('administrative_area_level_1'))?.short_name;
                        const postalCode = addressComponents.find(comp => comp.types.includes('postal_code'))?.short_name;
                        const country = addressComponents.find(comp => comp.types.includes('country'))?.long_name;
                        const area = addressComponents.find(comp => comp.types.includes('sublocality')|| comp.types.includes('route') || comp.types.includes('sublocality_level_1')|| comp.types.includes('neighborhood'))?.short_name;
                        const locationName = `${area ? area + ', ' : ''}${city ? city + ' -': ''} ${state ? state : ''}`;

                        wire.set('latitude', latitude);
                        wire.set('longitude', longitude);
                        wire.set('locationName', locationName);  // Set the location name in the component
                        wire.set('locationBlocked', false);
                        wire.set('locationFetch', false);
                        wire.set('city', city);
                        wire.set('state', state);
                        wire.set('country', country);
                        wire.set('postal_code', postalCode);

                    }
                })
                .catch(error => {
                    console.error("Error fetching the location name:", error);
                });
            },
            fetchLocationFromNominatim(latitude, longitude, wire) {
                    // Call Nominatim reverse geocoding API to fetch the location name
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.address) {
                            const address = data.address;
                            const city = address.city || address.town || address.village || address.county;

                            const state = address.state;
                            const country_code = address.country_code;

                            const country = address.country;

                            const postalCode = address.postcode;
                            const locationName = `${city ? city + ' -' : ''}${state ? state  : ''}`;

                            wire.set('latitude', latitude);
                            wire.set('longitude', longitude);
                            wire.set('locationName', locationName);
                            wire.set('locationDisplayName', data.display_name);
                            wire.set('locationBlocked', false);
                            wire.set('locationFetch', false);
                            wire.set('city', city);
                            wire.set('state', state);
                            wire.set('country', country);
                            wire.set('postal_code', postalCode);
                            wire.set('country_code', country_code);
                            if(!city){
                            alert("{{__('messages.t_city_not_found')}}");
                            }
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching the location name:", error);
                    });
            },
            updateLocations(inputValue) {
                if (!inputValue) {
                    this.locations = [];
                    return;
                }

                // Use Nominatim API for geocoding
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${inputValue}&limit=10`)
                    .then(response => response.json())
                    .then(data => {
                        this.locations = data.map(item => {
                            const description = `${item.display_name}`;
                            const postalCode = item.addresstype == 'postcode' ? item.name : false; // Extract the postal code if available

                            return {
                                name: item.display_name,
                                lat: item.lat,
                                lon: item.lon,
                                postalCode, // Add postal code to the returned object
                                description
                            };
                        });

                    })
                    .catch(error => {
                        console.error("Error fetching locations:", error);
                        this.locations = [];
                    });
            },

            selectLocation(selectedLocation, wire, type) {
                const latitude = selectedLocation.lat;
                const longitude = selectedLocation.lon;
                const displayName = selectedLocation.name;

                // Use Nominatim’s reverse geocoding API to get detailed address components
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        const address = data.address;
                        const city = address.city || address.town || address.village || address.suburb || address.county;
                        const country = address.country;
                        const state = address.state;

                        const postalCode = selectedLocation.postalCode ? selectedLocation.postalCode : address.postcode;
                       // Create an array with all location components
                       const locationComponents = [city, state, postalCode, country];

                        // Filter out undefined or empty components and join them with a comma
                        const locationName = `${city ? city + ' -' : ''}${state ? state  : ''}`;


                        wire.set('locationName', locationName);
                        wire.set('latitude', latitude);
                        wire.set('longitude', longitude);
                        wire.set('locationDisplayName', displayName);
                        wire.set('city', city);
                        wire.set('country', country);
                        wire.set('state', state);
                        wire.set('postal_code', postalCode);
                        if(!city){
                            alert("{{__('messages.t_city_not_found')}}");
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching detailed location:", error);
                    });
            }


        });
</script>
@endscript
