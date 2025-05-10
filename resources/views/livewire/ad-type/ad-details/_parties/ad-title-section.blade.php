<section class="py-6 border-y border-gray-200 dark:border-white/20 classic:border-black ">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row  max-w-full justify-between">
            <div class="flex-none md:flex items-center gap-x-2 w-fit md:w-[80%]">
                <div class="flex justify-between mb-4 md:mb-0">
                    @if (!optional(current($customizationSettings->ad_detail_page))['enable_price_below_image'])
                    @if ($ad->adType?->disable_price_type == false && !in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
                    <div class="rtl:justify-end rtl:scale-x-[-1]">
                        <x-price
                            value="{{ formatPriceWithCurrency($ad->price) }}"
                            type_id="{{ $ad->price_type_id }}" label="{{ $ad->priceType->label }}"
                            has_prefix="{{ $ad?->adType?->has_price_suffix }}"
                            price_suffix="{{ $ad->price_suffix }}" offer_enabled="{{ $ad->isEnabledOffer() }}"
                            offer_price="{{ $ad->offer_price ? formatPriceWithCurrency($ad->offer_price) : null }}"
                            offer_percentage="{{ $ad->getOfferPercentage() }}"
                            ad_type="{{$ad->adType?->marketplace}}" />
                    </div>
                    @endif

                    @if (in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
                    <x-price
                        value="{{ formatPriceWithCurrency($ad->price) }}"
                        type_id="{{ $ad->price_type_id }}" label="{{ $ad->priceType->label }}"
                        has_prefix="{{ $ad?->adType?->has_price_suffix }}"
                        price_suffix="{{ $ad->price_suffix }}" offer_enabled="{{ $ad->isEnabledOffer() }}"
                        offer_price="{{ $ad->offer_price ? formatPriceWithCurrency($ad->offer_price) : null }}"
                        offer_percentage="{{ $ad->getOfferPercentage() }}"
                        ad_type="{{$ad->adType?->marketplace}}" />
                    @endif
                    @endif
                    @if (!optional(current($customizationSettings->ad_detail_page))['enable_mobile_view_ad_action_in_below_ad'])
                    <div class="md:hidden">
                        <x-ad.share-report :$isFavourited :$ad />
                    </div>
                    @endif

                </div>
                <div>
                    <h1 class="md:text-2xl text-xl mb-4 md:mb-0 font-semibold hidden md:block ">{{ $this->ad->title }}</h1>
                    @if (optional(current($customizationSettings->ad_detail_page))['enable_location_below_title'])
                    @unless ($ad?->adType?->disable_location)
                    <div class="flex gap-2 mt-2 flex-wrap">
                        <x-icon-location class="w-6 h-6 hidden md:block dark:text-gray-400" />
                        <x-icon-location class="w-6 h-6 md:hidden" /> {{ $ad->location_name }}
                        <div class="text-gray-600">{{ __('messages.t_posted_on') }}
                            {{ $ad?->posted_date?->diffForHumans() }}
                        </div>
                    </div>
                    @endunless
                    @endif
                </div>
            </div>

            @if (!optional(current($customizationSettings->ad_detail_page))['enable_location_below_title'])
            <div class="flex md:flex-col md:items-end md:w-[20%]">
                <div class="md:flex items-center justify-between md:gap-x-2 w-full">
                    @unless ($ad?->adType?->disable_location)
                    <x-icon-location class="w-6 h-6 hidden md:block dark:text-gray-400" />
                    @endunless
                    <div class="flex md:flex-col justify-between ">
                        @unless ($ad?->adType?->disable_location)
                        <div class="flex gap-1">
                            <x-icon-location class="w-6 h-6 md:hidden" />
                            <span>{{ $ad->location_name }} </span>
                        </div>
                        @endunless
                        <div class="text-gray-600">{{ __('messages.t_posted_on') }}
                            {{ $ad?->posted_date?->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
