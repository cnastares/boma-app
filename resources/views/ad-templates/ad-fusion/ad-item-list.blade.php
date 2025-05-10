<a href="{{ route('ad.overview', ['slug' => $ad->slug, 'ref' => $ref]) }}"
    aria-label="{{$ad->title}}"
    class="w-full shadow-sm flex-none bg-white rounded-xl hover:shadow-md border dark:border-white/10 dark:bg-gray-900 classic:border-black classic:border {{$homeSettings->enable_hover_animation?'classic-hover-shadow':''}} relative flex flex-col md:pb-0 " aria-label="{{$ad->title}}">
    <div class="w-full flex md:pb-0">

    <div class="pb-0 p-2 md:p-3  md:pb-0 relative  md:w-auto md:h-auto flex-none">
        @php
        $imageProperties = $ad->image_properties;
        $altText = $imageProperties['1'] ?? $ad->title;
        @endphp
        <img src="{{ $ad->primaryImage ?? asset('/images/placeholder.jpg') }}" alt="{{ $altText }}"
            class="aspect-square object-cover h-28 flex w-full md:h-[12rem] rounded-xl">
        <div
            class="bg-[#b0b0b085] flex gap-2 w-fit px-1 py-[0.10rem] items-center rounded-md absolute top-4 right-4 z-[1]"
            x-data x-tooltip="{
                content: '{{__('messages.t_tooltip_no_of_images')}}',
                theme: $store.theme,
            }">
            <x-heroicon-o-camera class="h-5 w-5" />
            <span>{{count($ad->images())}}</span>
        </div>
    </div>

    <div class="flex-grow flex flex-col">
        <div class="flex-grow  border-gray-200  dark:border-white/10 classic:border-black mt-auto">
            <div class="px-2 md:px-3 py-3 h-full flex flex-col ">
                <h3 class="mb-2 text-sm md:text-base line-clamp-{{$adTemplateSettings->max_line}} break-all font-semibold">{{ $ad->title }} </h3>
                @if($isUrgent || $isFeatured)
                <div class="mb-2 flex gap-1">
                    @if($isUrgent)
                    <div x-data x-tooltip="{
                        content: '{{__('messages.t_tooltip_urgent_ad')}}',
                        theme: $store.theme,
                    }"
                        class="px-2 py-1 whitespace-nowrap text-xs  font-normal border classic:border-black border-transparent  dark:border-white/10  rounded-3xl w-fit  bg-red-600 text-black "
                        @if ($urgentAdColors)
                        style="{{$urgentAdColors->background_color?'background:'.$urgentAdColors->background_color:'#DC2626'}} ;{{$urgentAdColors->text_color?'color:'.$urgentAdColors->text_color:'#000000;'}}"
                        @endif>
                        {{ __('messages.t_urgent_ad') }}
                    </div>
                    @endif

                    @if($isFeatured)
                    <div x-data x-tooltip="{
                        content: '{{__('messages.t_tooltip_featured_ad')}}',
                        theme: $store.theme,
                    }"
                        class="px-1.5 py-1 whitespace-nowrap text-xs font-normal border classic:border-black border-transparent  dark:border-white/10 rounded-3xl w-fit  bg-yellow-400 text-black"
                        @if ($featureAdColors)
                        style="{{$featureAdColors->background_color?'background:'.$featureAdColors->background_color:'#FACC15'}}; {{$featureAdColors->text_color?'color:'.$featureAdColors->text_color:'#000000;'}}"
                        @endif>
                        {{ __('messages.t_featured_ad') }}
                    </div>
                    @endif
                </div>
                @endif
                @if(is_vehicle_rental_active() && has_plugin_vehicle_rental_marketplace())
                <div class="flex font-light  mb-2 text-sm truncate ">
                    <span class="">{!! $ad?->mileage ? $ad?->mileage.'&nbsp;'.__('messages.t_mileage_prefix'). '&nbsp;|&nbsp;' :'' !!} </span>
                    <span class="">{!! $ad?->fuelType?->name ? $ad?->fuelType?->name .'&nbsp;|&nbsp;':'' !!}</span>
                    <span class="">{{ $ad?->transmission?->name }}</span>
                </div>
                @endif
                <div class="mt-auto">
                    @if (is_ecommerce_active())
                    <div class="flex items-center mb-2 text-sm ">
                        <span class="font-light ml-1 line-clamp-1">{{ $ad?->category?->name }}</span>
                    </div>
                    @endunless
                    @unless ($ad?->adType?->disable_location)
                    <div class="flex items-center mb-2 text-sm " x-tooltip="{
                        content: '{{__('messages.t_tooltip_location')}}',
                        theme: $store.theme,
                    }">
                        <x-icon-pin-location class="w-5 h-5 dark:text-gray-500" />
                        <span class="font-light ml-1 line-clamp-1">{{ $ad->location_name }}</span>
                    </div>
                    @endunless
                    <span class=" md:block text-muted dark:text-gray-400 text-xs">@lang('messages.t_published_on') {{
                        \Carbon\Carbon::parse($ad->posted_date)->translatedFormat('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div>
        @if (($ad->adType?->disable_price_type!=true || $isFavourited) && !in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
        <div
            class="flex justify-between items-center px-3 py-2 md:py-3 mt-3  border-t md:static  border-gray-200  dark:border-white/10 classic:border-black">
            <div class="flex items-center gap-x-2 justify-between w-full">
                @if ($ad->adType?->disable_price_type!=true)

                @include('ad-templates.ad-fusion.price',[
                'value'=>config('app.currency_symbol').' '. \Number::format(floor($ad->price), locale: $paymentSettings->currency_locale),
                'type_id'=>$ad->price_type_id, 'label'=>$ad->priceType->label,
                'has_prefix'=>$ad?->category?->has_price_suffix, 'price_suffix'=>$ad->price_suffix,
                'offer_enabled'=>$ad->isEnabledOffer(),
                'offer_price'=>$ad->offer_price?config('app.currency_symbol').' '. \Number::format(floor($ad->offer_price), locale: $paymentSettings->currency_locale):null,
                'offer_percentage'=>$ad->getOfferPercentage(),
                'ad_type' => $ad->adType?->marketplace
                ])
                @endif

                <div class="ml-auto">
                    <div @click.prevent="$wire.addToFavourites" class="cursor-pointer " x-data x-tooltip="{
                        content: '{{$isFavourited?__('messages.t_tooltip_favourited'):__('messages.t_tooltip_favourite')}}',
                        theme: $store.theme,
                    }">
                        @if($isFavourited)
                        <x-heroicon-s-heart class="w-6 h-6 stroke-1" />
                        @else
                        <x-heroicon-o-heart class="w-6 h-6 dark:text-gray-400 stroke-1" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if (in_array($ad->adType?->marketplace,  [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
        <div
            class="flex justify-between items-center px-3 py-2 md:py-3  border-t md:static  border-gray-200  dark:border-white/10 classic:border-black">
            <div class="flex items-center gap-x-2 justify-between w-full">
                @include('ad-templates.ad-fusion.price',[
                'value'=>config('app.currency_symbol').' '. \Number::format(floor($ad->price), locale: $paymentSettings->currency_locale),
                'type_id'=>$ad->price_type_id,
                'label'=> null,
                'has_prefix'=> null,
                'price_suffix'=> null,
                'offer_enabled'=> $ad->isEnabledOffer(),
                'offer_price'=>$ad->offer_price?config('app.currency_symbol').' '. \Number::format(floor($ad->offer_price), locale: $paymentSettings->currency_locale):null,
                'offer_percentage'=>$ad->getOfferPercentage(),
                'ad_type' => $ad->adType?->marketplace
                ])

                <div class="ml-auto">
                    <div @click.prevent="$wire.addToFavourites" class="cursor-pointer ">
                        @if($isFavourited)
                        <x-heroicon-s-heart class="w-6 h-6 stroke-1" />
                        @else
                        <x-heroicon-o-heart class="w-6 h-6 dark:text-gray-400 stroke-1" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</a>
