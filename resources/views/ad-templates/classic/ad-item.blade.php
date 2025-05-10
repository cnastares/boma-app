<a href="{{ route('ad.overview', ['slug' => $ad->slug, 'ref' => $ref]) }}"
    aria-label="{{$ad->title}}"
    class="w-full shadow-sm flex-none bg-white rounded-xl hover:shadow-md  dark:bg-gray-900 classic:border-black classic:border {{$homeSettings->enable_hover_animation?'classic-hover-shadow':''}} relative flex flex-col md:pb-0 pb-[2.5rem] border dark:border-white/10" aria-label="{{$ad->title}}" wire:click="saveClicks">

    <div class="pb-0 p-2 md:p-3  md:pb-0 relative  md:w-auto md:h-auto flex-none">
        <div class="absolute top-4 right-4 z-[1] {{!$isFavourited?' rounded-full':''}}">
            <x-ad.favourite-ad :$isFavourited />
        </div>

        @if ($isUrgent)
        <div class="px-2 py-1 whitespace-nowrap text-xs md:text-sm font-medium border classic:border-black border-transparent  dark:border-white/10 absolute top-2 md:rounded-none md:top-6 left-3 right-3 md:right-auto md:left-6 bg-red-600 text-black"
            x-data x-tooltip="{
            content: '{{__('messages.t_tooltip_urgent_ad')}}',
            theme: $store.theme,
        }"
            @if ($urgentAdColors)
            style="{{$urgentAdColors->background_color?'background:'.$urgentAdColors->background_color:'#DC2626'}} ;{{$urgentAdColors->text_color?'color:'.$urgentAdColors->text_color:'#000000;'}}"
            @endif>
            {{ __('messages.t_urgent_ad') }}
        </div>
        @endif

        @if ($isFeatured)
        <div class="px-2 py-1 whitespace-nowrap text-xs md:text-sm font-medium border classic:border-black border-transparent  dark:border-white/10 absolute bottom-0 md:rounded-none md:bottom-4 md:right-auto left-3 right-3 md:left-6 bg-yellow-400 text-black"
            x-data x-tooltip="{
            content: '{{__('messages.t_tooltip_featured_ad')}}',
            theme: $store.theme,
        }"
            @if ($featureAdColors)
            style="{{$featureAdColors->background_color?'background:'.$featureAdColors->background_color:'#FACC15'}}; {{$featureAdColors->text_color?'color:'.$featureAdColors->text_color:'#000000;'}}"
            @endif>
            {{ __('messages.t_featured_ad') }}
        </div>
        @endif
        @php
        $imageProperties = $ad->image_properties;
        $altText = $imageProperties['1'] ?? $ad->title;
        @endphp
        <img src="{{ $ad->primaryImage ?? asset('/images/placeholder.jpg') }}" alt="{{ $altText }}"
            class="aspect-square object-cover h-32 flex w-full md:h-[12rem] rounded-xl">
    </div>

    <div class="flex-grow flex flex-col">
        <div class="flex-grow  border-gray-200  dark:border-white/10 classic:border-black mt-auto">
            <div class="px-2 md:px-3 py-3 h-full flex flex-col ">
                <h3 class="mb-1 text-sm md:text-base line-clamp-{{$adTemplateSettings->max_line}} font-semibold">{{ $ad->title }}</h3>
                @if(is_vehicle_rental_active() && has_plugin_vehicle_rental_marketplace())
                <div class="flex font-light  mb-2 text-sm truncate ">
                    <span class="">{!! $ad?->mileage ? $ad?->mileage.'&nbsp;'.__('messages.t_mileage_prefix'). '&nbsp;|&nbsp;' :'' !!} </span>
                    <span class="">{!! $ad?->fuelType?->name ? $ad?->fuelType?->name .'&nbsp;|&nbsp;':'' !!}</span>
                    <span class="">{{ $ad?->transmission?->name }}</span>
                </div>
                @endif
                @if (is_ecommerce_active())
                <div class="flex items-center mb-2 text-sm ">
                    <span class="font-light ml-1 line-clamp-1">{{ $ad?->category?->name }}</span>
                </div>
                @endunless
                @unless ($ad?->adType?->disable_location && $ad->location_name)
                <div class="flex items-center mb-2 text-sm ">
                    <x-icon-pin-location class="w-5 h-5 dark:text-gray-500" />
                    <span class="font-light ml-1 line-clamp-1">{{ $ad->location_name }}</span>
                </div>
                @endunless
                <span
                    class=" md:block text-muted dark:text-gray-400 text-sm">{{ \Carbon\Carbon::parse($ad->posted_date)->translatedFormat('M j') }}</span>
            </div>
        </div>

        @if (($ad->adType?->disable_price_type != true || $isFavourited) && !in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
        <div
            class=" rtl:justify-end  flex justify-between items-center px-3 py-2 md:py-3  border-t md:static absolute left-0 right-0 bottom-0 border-gray-200  dark:border-white/10 classic:border-black">
            <div class="flex items-center gap-x-2 justify-between w-full">

                @if ($ad->adType?->disable_price_type != true)
                <x-price
                    value="{{ formatPriceWithCurrency($ad->price) }}"
                    type_id="{{ $ad->price_type_id }}" label="{{ $ad->priceType->label }}"
                    has_prefix="{{ $ad?->adType?->has_price_suffix }}"
                    price_suffix="{{ $ad->price_suffix }}"
                    offer_enabled="{{$ad->isEnabledOffer()}}"
                    offer_price="{{$ad->offer_price ? formatPriceWithCurrency($ad->offer_price): null}}"
                    offer_percentage="{{$ad->getOfferPercentage()}}"
                    ad_type="{{$ad->adType?->marketplace}}" />
                @endif
                @if(is_vehicle_rental_active() && has_plugin_vehicle_rental_marketplace())
                <p class="text-sm">
                    {{ $ad->price_suffix=='day'? __('messages.t_per_day'):'' }}
                </p>
                @endif
                {{-- <div class="hidden">
                    <x-ad.favourite-ad :$isFavourited />
                </div> --}}
            </div>
        </div>
        @endif

        @if (in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
        <div
            class="flex justify-between items-center px-3 py-2 md:py-3  border-t md:static  border-gray-200  dark:border-white/10 classic:border-black">
            <div class="flex items-center gap-x-2 justify-between w-full">
                <x-price
                    value="{{ formatPriceWithCurrency($ad->price) }}"
                    type_id="{{ $ad->price_type_id }}" label="{{ $ad->priceType->label }}"
                    has_prefix="{{ $ad?->adType?->has_price_suffix }}"
                    price_suffix="{{ $ad->price_suffix }}"
                    offer_enabled="{{$ad->isEnabledOffer()}}"
                    offer_price="{{$ad->offer_price ? formatPriceWithCurrency($ad->offer_price) : null}}"
                    offer_percentage="{{$ad->getOfferPercentage()}}"
                    ad_type="{{$ad->adType?->marketplace}}" />
            </div>
        </div>
        @endif
    </div>
</a>
