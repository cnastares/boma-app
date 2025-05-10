<div x-data="{
    isCopied: false,
    copy() {
        const url = '{{ url()->current() }}';
        var _this = this;
        navigator.clipboard.writeText(url).then(function() {
            _this.isCopied = true;
            setTimeout(() => {
                _this.isCopied = false;
            }, 2000);
        }, function(err) {
            console.error('Failed to copy text: ', err);
        });
    }
}">

    <x-page-header title="{{ $this->ad->title }}" isMobileHidden />

    <livewire:layout.header isMobileHidden lazy />

    @if ($adPlacementSettings->after_header)
        <div class="container mx-auto px-4 py-6 flex items-center justify-center " role="complementary" aria-label="{{ __('messages.t_aria_label_header_advertisement')}}">
            {!! $adPlacementSettings->after_header !!}
        </div>
    @endif


    <div class="py-6 border-y border-gray-200 dark:border-white/20 classic:border-black ">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row  max-w-full justify-between">
                <div class="flex-none md:flex items-center gap-x-2 w-fit md:w-[90%]">
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

                        @if (in_array($ad->adType?->marketplace,  [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
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
                <div class="flex md:flex-col md:items-end md:w-[10%]">
                    <div class="md:flex items-center md:gap-x-2 w-full">
                        @unless ($ad?->adType?->disable_location)
                            <x-icon-location class="w-6 h-6 hidden md:block dark:text-gray-400" />
                        @endunless
                        <div class="flex md:flex-col justify-between ">
                            @unless ($ad?->adType?->disable_location)
                                <div class="flex">
                                    <x-icon-location class="w-6 h-6 md:hidden" /> {{ $ad->location_name }}
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
    </div>

    <div class="container mx-auto md:px-4 px-2">
        <div class="mt-6">
            <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
        </div>
    </div>

    {{-- Modals (View review) --}}
    @if (isECommercePluginEnabled())
        <x-filament::modal id="view-review" width="5xl" :close-button="false">
            <div
                class="bg-white rounded-ss-xl rounded-se-xl md:rounded-xl w-full h-full md:min-h-[22rem] md:max-h-[33rem] dark:bg-gray-800 ">
                <div class=" flex justify-between items-center sticky top-0 bg-white dark:bg-gray-900">
                    <div class=" flex items-center gap-x-2">
                        <svg class="w-6 h-6" width="20" height="20" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.47998 1.49897C9.52227 1.3958 9.5943 1.30755 9.6869 1.24543C9.7795 1.18331 9.88848 1.15015 9.99998 1.15015C10.1115 1.15015 10.2205 1.18331 10.3131 1.24543C10.4057 1.30755 10.4777 1.3958 10.52 1.49897L12.645 6.60997C12.6848 6.70561 12.7501 6.78841 12.834 6.84928C12.9178 6.91015 13.0167 6.94672 13.12 6.95497L18.638 7.39697C19.137 7.43697 19.339 8.05997 18.959 8.38497L14.755 11.987C14.6764 12.0542 14.6179 12.1417 14.5858 12.2399C14.5537 12.3382 14.5493 12.4434 14.573 12.544L15.858 17.929C15.8838 18.037 15.877 18.1503 15.8385 18.2545C15.8 18.3587 15.7315 18.4491 15.6416 18.5144C15.5517 18.5797 15.4445 18.6168 15.3335 18.6212C15.2225 18.6256 15.1127 18.597 15.018 18.539L10.293 15.654C10.2048 15.6001 10.1034 15.5715 9.99998 15.5715C9.89659 15.5715 9.79521 15.6001 9.70698 15.654L4.98198 18.54C4.88724 18.598 4.77743 18.6266 4.66644 18.6222C4.55544 18.6178 4.44823 18.5807 4.35835 18.5154C4.26847 18.4501 4.19994 18.3597 4.16143 18.2555C4.12292 18.1513 4.11615 18.038 4.14198 17.93L5.42698 12.544C5.45081 12.4434 5.44643 12.3381 5.41432 12.2399C5.38221 12.1416 5.32362 12.0541 5.24498 11.987L1.04098 8.38497C0.956324 8.3128 0.894988 8.21714 0.864741 8.11009C0.834494 8.00304 0.836696 7.88942 0.87107 7.78362C0.905443 7.67782 0.970441 7.58461 1.05783 7.51578C1.14522 7.44695 1.25107 7.4056 1.36198 7.39697L6.87998 6.95497C6.98323 6.94672 7.0822 6.91015 7.16601 6.84928C7.24981 6.78841 7.3152 6.70561 7.35498 6.60997L9.47998 1.49897Z"
                                fill="#FDAE4B" stroke="black" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class=" text-xl font-semibold flex items-center gap-x-1 pt-1">
                            <div class="">{{ number_format($ad->customerReviews()->avg('rating'), 1) }}</div>
                            <div class=" w-1 h-1 bg-black dark:bg-gray-900 rounded-full"></div>
                            <div @click="$dispatch('open-modal', {id: 'view-review'});"
                                class="cursor-pointer font-semibold underline underline-offset-1 whitespace-nowrap">
                                {{ $ad->customerReviews()->count() }} Reviews
                            </div>
                        </div>
                    </div>
                    <svg @click="$dispatch('close-modal', {id: 'view-review'});" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class=" w-7 h-7 cursor-pointer text-gray-400 hover:text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </div>

                <div class=" grid grid-cols-1 md:grid-cols-2 py-5 dark:bg-gray-900">
                    <div class=" md:sticky md:top-[92px] h-fit border-b border-[#B0B0B0] md:border-none pb-5">
                        <div class=" flex gap-x-4">
                            <div>
                                @php
                                    $imageProperties = $ad->image_properties;
                                    $altText = $imageProperties['1'] ?? $ad->title;
                                @endphp
                                {{-- <Saspect-square object-cover h-32 flex w-full md:h-[7rem] rounded-xl"> --}}
                            </div>
                            <div class=" flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold">{{ $ad->title }}</h3>
                                    <span class=" text-[#71717A]">{{ $ad?->category?->name }}</span>
                                    <div class=" text-sm text-[#71717A]">Seller: {{ $ad->user->name }}</div>
                                </div>
                                <div class=" flex items-center gap-x-2">
                                    @if ($ad->isEnabledOffer() && $ad->offer_price)
                                        <span
                                            class=" text-sm font-semibold">{{ $ad->offer_price ? formatPriceWithCurrency($ad->offer_price) : null }}</span>

                                        <span
                                            class=" text-sm text-[#71717A] line-through">{{ formatPriceWithCurrency($ad->price) }}</span>
                                        @if ($ad->getOfferPercentage())
                                            <span
                                                class=" text-sm text-[#FDAE4B] font-semibold">{{ $ad->getOfferPercentage() }}
                                                % OFF</span>
                                        @endif
                                    @else
                                        <span
                                            class=" text-sm font-semibold">{{ formatPriceWithCurrency($ad->price) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class=" grid grid-cols-1 gap-y-5 pt-5">
                            <div class="text-lg font-semibold flex items-center">
                                <a href="#" class="flex items-center gap-x-2 cursor-pointer outline-none group">
                                    <div
                                        class="bg-gray-200 dark:bg-black dark:text-gray-100 text-black border rounded-full h-8 w-8 flex items-center justify-center">
                                        <span>{{ substr($ad->user->name, 0, 1) }}</span>
                                    </div>
                                    <span class=" group-hover:underline">{{ $ad->user->name }}</span>
                                </a>
                            </div>

                            <div class="flex items-center gap-x-2 ml-2">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                    data-slot="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z">
                                    </path>
                                </svg> <span class="text-sm md:text-base">{{ __('messages.t_member_since') }}
                                    {{ \Carbon\Carbon::parse($ad->user->created_at)->translatedFormat('F Y') }}</span>
                            </div>

                            @if ($ad->user->email_verified_at)
                                <div class="flex items-center gap-x-2 ml-2">
                                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        aria-hidden="true" data-slot="icon">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75">
                                        </path>
                                    </svg>
                                    <span class="text-sm md:text-base">{{ __('messages.t_email_verified') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- review section  --}}
                    <div
                        class="md:min-h-[14rem] md:max-h-[25rem] overflow-y-auto md:pl-5 grid grid-cols-1 gap-y-5 md:border-l md:border-[#B0B0B0] pt-5 md:pt-0">
                        @foreach ($ad->customerReviews()->get() as $review)
                            <div>
                                <div class=" flex items-center gap-x-1.5">
                                    <div class="">
                                        @if ($review->user->profile_image)
                                            <img src="{{ $review->user->profile_image }}"
                                                alt="{{ $review->user->name }}"
                                                class="rounded-full w-10 h-10 border border-black">
                                        @else
                                            <div
                                                class="bg-gray-200 dark:bg-black dark:text-gray-100 text-black border rounded-full w-10 h-10 flex items-center justify-center">
                                                <span>{{ substr($review->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <span class=" text-lg font-medium">{{ $review->user->name }}</span>
                                        <div class=" flex items-center gap-x-1 pt-1">
                                            @for ($i = 0; $i < $review->rating; $i++)
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M9.47998 1.49897C9.52227 1.3958 9.5943 1.30755 9.6869 1.24543C9.7795 1.18331 9.88848 1.15015 9.99998 1.15015C10.1115 1.15015 10.2205 1.18331 10.3131 1.24543C10.4057 1.30755 10.4777 1.3958 10.52 1.49897L12.645 6.60997C12.6848 6.70561 12.7501 6.78841 12.834 6.84928C12.9178 6.91015 13.0167 6.94672 13.12 6.95497L18.638 7.39697C19.137 7.43697 19.339 8.05997 18.959 8.38497L14.755 11.987C14.6764 12.0542 14.6179 12.1417 14.5858 12.2399C14.5537 12.3382 14.5493 12.4434 14.573 12.544L15.858 17.929C15.8838 18.037 15.877 18.1503 15.8385 18.2545C15.8 18.3587 15.7315 18.4491 15.6416 18.5144C15.5517 18.5797 15.4445 18.6168 15.3335 18.6212C15.2225 18.6256 15.1127 18.597 15.018 18.539L10.293 15.654C10.2048 15.6001 10.1034 15.5715 9.99998 15.5715C9.89659 15.5715 9.79521 15.6001 9.70698 15.654L4.98198 18.54C4.88724 18.598 4.77743 18.6266 4.66644 18.6222C4.55544 18.6178 4.44823 18.5807 4.35835 18.5154C4.26847 18.4501 4.19994 18.3597 4.16143 18.2555C4.12292 18.1513 4.11615 18.038 4.14198 17.93L5.42698 12.544C5.45081 12.4434 5.44643 12.3381 5.41432 12.2399C5.38221 12.1416 5.32362 12.0541 5.24498 11.987L1.04098 8.38497C0.956324 8.3128 0.894988 8.21714 0.864741 8.11009C0.834494 8.00304 0.836696 7.88942 0.87107 7.78362C0.905443 7.67782 0.970441 7.58461 1.05783 7.51578C1.14522 7.44695 1.25107 7.4056 1.36198 7.39697L6.87998 6.95497C6.98323 6.94672 7.0822 6.91015 7.16601 6.84928C7.24981 6.78841 7.3152 6.70561 7.35498 6.60997L9.47998 1.49897Z"
                                                        fill="#FDAE4B" stroke="black" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            @endfor
                                            @for ($i = 0; $i < 5 - $review->rating; $i++)
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M9.47998 1.49897C9.52227 1.3958 9.5943 1.30755 9.6869 1.24543C9.7795 1.18331 9.88848 1.15015 9.99998 1.15015C10.1115 1.15015 10.2205 1.18331 10.3131 1.24543C10.4057 1.30755 10.4777 1.3958 10.52 1.49897L12.645 6.60997C12.6848 6.70561 12.7501 6.78841 12.834 6.84928C12.9178 6.91015 13.0167 6.94672 13.12 6.95497L18.638 7.39697C19.137 7.43697 19.339 8.05997 18.959 8.38497L14.755 11.987C14.6764 12.0542 14.6179 12.1417 14.5858 12.2399C14.5537 12.3382 14.5493 12.4434 14.573 12.544L15.858 17.929C15.8838 18.037 15.877 18.1503 15.8385 18.2545C15.8 18.3587 15.7315 18.4491 15.6416 18.5144C15.5517 18.5797 15.4445 18.6168 15.3335 18.6212C15.2225 18.6256 15.1127 18.597 15.018 18.539L10.293 15.654C10.2048 15.6001 10.1034 15.5715 9.99998 15.5715C9.89659 15.5715 9.79521 15.6001 9.70698 15.654L4.98198 18.54C4.88724 18.598 4.77743 18.6266 4.66644 18.6222C4.55544 18.6178 4.44823 18.5807 4.35835 18.5154C4.26847 18.4501 4.19994 18.3597 4.16143 18.2555C4.12292 18.1513 4.11615 18.038 4.14198 17.93L5.42698 12.544C5.45081 12.4434 5.44643 12.3381 5.41432 12.2399C5.38221 12.1416 5.32362 12.0541 5.24498 11.987L1.04098 8.38497C0.956324 8.3128 0.894988 8.21714 0.864741 8.11009C0.834494 8.00304 0.836696 7.88942 0.87107 7.78362C0.905443 7.67782 0.970441 7.58461 1.05783 7.51578C1.14522 7.44695 1.25107 7.4056 1.36198 7.39697L6.87998 6.95497C6.98323 6.94672 7.0822 6.91015 7.16601 6.84928C7.24981 6.78841 7.3152 6.70561 7.35498 6.60997L9.47998 1.49897Z"
                                                        fill="#DEDEDE" stroke="black" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                {{-- <p class="pt-3">{{ $review->feedback }}</p> --}}
                            </div>
                        @endforeach
                        @if ($ad->customerReviews()->count() == 0)
                            <div class="flex flex-col items-center justify-center p-10 w-full">
                                <x-not-found description="{{ __('messages.t_review_is_empty') }}" />
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-filament::modal>
    @endif

    <div class="md:pb-10 md:pt-6 {{!$adPlacementSettings->before_footer?'pb-32':'pb-0'}}">
        <div class="container mx-auto md:px-4">

            @if ($ownerView)
                <div class="flex items-center p-4 mb-8 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800"
                    role="alert">
                    <x-heroicon-o-exclamation-circle class="w-6 h-6" />
                    <div class="ml-3 text-sm font-medium">
                        @if ($this->ad->status->value == 'expired')
                            {{ __('messages.t_listing_expired', [
                                'title' => $this->ad->title,
                                'date' => $this->ad->expires_at->format('F j, Y'),
                            ]) }}
                        @elseif($this->ad->status->value == 'inactive')
                            {{ __('messages.t_item_deactivated', ['title' => $this->ad->title]) }}
                        @else
                            {{ __('messages.t_item_marked_as_status', [
                                'title' => $this->ad->title,
                                'status' => __('messages.t_' . $this->ad->status->value . '_status'),
                            ]) }}
                        @endif
                        <a href="{{ route('post-ad') }}"
                            class="font-semibold underline hover:no-underline">{{ __('messages.t_post_new_ad') }}</a>.
                    </div>
                </div>
            @endif

            @if (Auth::id() == $this->ad->user_id && $this->ad->status->value == 'active')
                <div class="py-2 px-4 md:px-0">
                    <livewire:ad.sell-faster id="{{ $this->ad->id }}" isHorizontal="{{ true }}" />
                </div>
            @endif

            <div
                class="grid grid-cols-7 bg-white md:ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:rounded-xl classic:ring-black ">
                <div
                    class="md:col-span-5 col-span-7  rtl:border rtl:border-r-0 rtl:border-l rtl:border-t-0 rtl:border-b-0 md:border-r border-gray-200 dark:border-white/20 classic:border-black relative">

                    @if (optional(current($customizationSettings->ad_detail_page))['enable_carousel'])
                    <x-ad.carousel-gallery :ad="$ad" :images="$ad->images()" :videoLink="$ad->video_link" class="" :image_properties="$ad->image_properties"
                    :ad_title="$ad->title" />
                    @else
                    <x-ad.gallery :images="$ad->images()" :videoLink="$ad->video_link" class="" :image_properties="$ad->image_properties"
                    :ad_title="$ad->title" :adId="$ad->id" />
                    @endif

                    @if (!optional(current($customizationSettings->ad_detail_page))['enable_favourite_move_to_ad_action'])
                    <div class="absolute top-4 right-4 !text-white">
                        <x-ad.favourite-ad :$isFavourited />
                    </div>
                    @endif

                    @if ($isUrgent)
                        <div x-tooltip="{
                            content: '{{__('messages.t_tooltip_urgent_ad')}}',
                            theme: $store.theme,
                        }"
                            class="px-2 py-1 text-sm font-medium border border-black absolute {{ $isUrgent && $isFeatured ? 'top-16' : 'top-6' }} left-6 bg-red-600 z-10 text-black"
                            @if ($urgentAdColors)
                            style="{{$urgentAdColors->background_color?'background:'.$urgentAdColors->background_color:'#DC2626'}} ;{{$urgentAdColors->text_color?'color:'.$urgentAdColors->text_color:'#000000;'}}"
                            @endif                            >
                            {{ __('messages.t_urgent_ad') }}
                        </div>
                    @endif

                    @if ($isFeatured)
                        <div x-tooltip="{
                            content: '{{__('messages.t_tooltip_featured_ad')}}',
                            theme: $store.theme,
                        }"
                            class="px-2 py-1 text-sm font-medium border border-black absolute top-6 left-6 bg-yellow-400 z-[1] text-black"
                            @if ($featureAdColors)
                            style="{{$featureAdColors->background_color?'background:'.$featureAdColors->background_color:'#FACC15'}}; {{$featureAdColors->text_color?'color:'.$featureAdColors->text_color:'#000000;'}}"
                            @endif>
                            {{ __('messages.t_featured_ad') }}
                        </div>
                    @endif

                    @if (is_vehicle_rental_active() && $vehicle?->transmission)
                        <div class="py-6 px-4">
                            <h3 class="text-lg mb-4 font-semibold">{{ __('messages.t_ad_details') }}:</h3>
                            <div class="space-y-3">
                                <div class=" space-x-2">
                                    <span
                                        class="font-medium whitespace-nowrap">{{ __('messages.t_transmission') }}:</span>
                                    <span
                                        class="text-gray-600 dark:text-gray-300 grid-cols-1 md:col-span-3">{{ $vehicle->transmission->name }}</span>
                                </div>
                                <div class=" space-x-2">
                                    <span
                                        class="font-medium whitespace-nowrap">{{ __('messages.t_fuel_type') }}:</span>
                                    <span
                                        class="text-gray-600 dark:text-gray-300 grid-cols-1 md:col-span-3">{{ $vehicle->fuelType->name }}</span>
                                </div>
                                <div class=" space-x-2">
                                    <span class="font-medium whitespace-nowrap">{{ __('messages.t_mileage') }}:</span>
                                    <span
                                        class="text-gray-600 dark:text-gray-300 grid-cols-1 md:col-span-3">{{ $vehicle->mileage . ' ' . __('messages.t_mileage_prefix') }}</span>
                                </div>
                                <div class=" flex items-start gap-x-2">
                                    <span
                                        class="font-medium whitespace-nowrap">{{ __('messages.t_vehicle_features') }}:</span>
                                    <div class="text-gray-600 dark:text-gray-300">
                                        @foreach ($vehicle->features as $features)
                                            {{ $features->name }}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                    <x-ad.description :description="$descriptionHtml" />

                    @php
                        $otherGroup = $fieldDetails['Other'] ?? []; // Collect 'Other' group separately if it exists
                        unset($fieldDetails['Other']); // Remove 'Other' from the main array to process it last
                    @endphp

                    @if(($ad->condition && $ad->category?->disable_condition != true) || ($otherGroup && count($otherGroup)) || ($fieldDetails && count($fieldDetails)))
                    <div class="space-y-4 px-4 pb-4 md:mb-8">
                        @if ($ad->condition && $ad->category?->disable_condition != true)
                            <div class=" space-x-2">
                                <span class="font-medium text-lg w-1/3">{{ __('messages.t_condition') }}: </span>
                                <span class="text-base w-2/3">{{ $ad->condition->name }}</span>
                            </div>
                        @endif

                        {{-- First render fields without a group (if any exist) --}}
                        @foreach ($otherGroup as $fieldDetail)
                            @if ($fieldDetail['value'])
                                <div wire:key="field-{{ $fieldDetail['field_id'] }}">
                                    <span
                                        class="font-medium text-lg break-all whitespace-nowrap">{{ $fieldDetail['field_name'] }}:</span>
                                    @if (is_array($fieldDetail['value']))
                                        <div class="flex gap-2">
                                            @foreach ($fieldDetail['value'] as $value)
                                                <span
                                                    class="inline-block bg-gray-200 hover:bg-gray-300 rounded-full px-3 py-1 text-sm md:text-base font-semibold text-gray-700 mr-2 mb-2 capitalize">{{ $value }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span
                                            class="text-sm md:text-base ml-2 break-all">{{ $fieldDetail['value'] }}</span>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        {{-- Then render fields with groups --}}
                        @foreach ($fieldDetails as $groupName => $fields)
                            <div
                                class="pt-4 pb-1 !mt-5  border-t border-gray-200 dark:border-white/20 classic:border-black">
                                <span class="font-semibold text-lg">{{ $groupName }}:</span>
                            </div>
                            @foreach ($fields as $fieldDetail)
                                @if ($fieldDetail['value'])
                                    <div wire:key="field-{{ $fieldDetail['field_id'] }}" class="flex items-center">
                                        <span class="font-medium text-lg">{{ $fieldDetail['field_name'] }}:</span>
                                        @if (is_array($fieldDetail['value']))
                                            <div class="flex gap-2">
                                                @foreach ($fieldDetail['value'] as $value)
                                                    <span
                                                        class="inline-block bg-gray-200 hover:bg-gray-300 rounded-full px-3 py-1 text-sm md:text-base font-semibold text-gray-700 mr-2 mb-2 capitalize">{{ $value }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span
                                                class="text-sm md:text-base ml-2 inline-block">{{ $fieldDetail['value'] }}</span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="md:col-span-2 col-span-7 ">
                    @if (is_ecommerce_active() && in_array($ad->adType?->marketplace,  [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE))
                        <div class="border border-gray-200 dark:border-white/20  classic:border-black border-l-0 border-r-0 border-t-0 rounded-none p-4"
                            x-data="{ policy: false, }">
                            <div class="flex justify-between items-center gap-x-5">
                                @if (isECommerceQuantityOptionEnabled())
                                    <div class="w-full">
                                        <div class="flex items-center mt-4 gap-x-2">
                                            <span class="text-sm md:text-base">{{ __('messages.t_quantity') }}</span>
                                        </div>
                                        <x-input wire:model.live='cartQuantity' min="1" type="number"
                                            max='{{ getECommerceMaximumQuantityPerItem() }}' value="1"
                                            class="my-3 border border-[#B0B0B0] classic:border-black dark:border-white/10 focus:ring-0 focus:!outline-none dark:focus-within:border-2 dark:focus-within:!border-primary-600 w-[70%] dark:!bg-zinc-800 !rounded" />
                                    </div>
                                @endif
                                <div
                                    class=" flex items-center gap-x-1 @if (isECommerceQuantityOptionEnabled()) mt-10 @else mb-5 @endif">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M9.47998 1.49897C9.52227 1.3958 9.5943 1.30755 9.6869 1.24543C9.7795 1.18331 9.88848 1.15015 9.99998 1.15015C10.1115 1.15015 10.2205 1.18331 10.3131 1.24543C10.4057 1.30755 10.4777 1.3958 10.52 1.49897L12.645 6.60997C12.6848 6.70561 12.7501 6.78841 12.834 6.84928C12.9178 6.91015 13.0167 6.94672 13.12 6.95497L18.638 7.39697C19.137 7.43697 19.339 8.05997 18.959 8.38497L14.755 11.987C14.6764 12.0542 14.6179 12.1417 14.5858 12.2399C14.5537 12.3382 14.5493 12.4434 14.573 12.544L15.858 17.929C15.8838 18.037 15.877 18.1503 15.8385 18.2545C15.8 18.3587 15.7315 18.4491 15.6416 18.5144C15.5517 18.5797 15.4445 18.6168 15.3335 18.6212C15.2225 18.6256 15.1127 18.597 15.018 18.539L10.293 15.654C10.2048 15.6001 10.1034 15.5715 9.99998 15.5715C9.89659 15.5715 9.79521 15.6001 9.70698 15.654L4.98198 18.54C4.88724 18.598 4.77743 18.6266 4.66644 18.6222C4.55544 18.6178 4.44823 18.5807 4.35835 18.5154C4.26847 18.4501 4.19994 18.3597 4.16143 18.2555C4.12292 18.1513 4.11615 18.038 4.14198 17.93L5.42698 12.544C5.45081 12.4434 5.44643 12.3381 5.41432 12.2399C5.38221 12.1416 5.32362 12.0541 5.24498 11.987L1.04098 8.38497C0.956324 8.3128 0.894988 8.21714 0.864741 8.11009C0.834494 8.00304 0.836696 7.88942 0.87107 7.78362C0.905443 7.67782 0.970441 7.58461 1.05783 7.51578C1.14522 7.44695 1.25107 7.4056 1.36198 7.39697L6.87998 6.95497C6.98323 6.94672 7.0822 6.91015 7.16601 6.84928C7.24981 6.78841 7.3152 6.70561 7.35498 6.60997L9.47998 1.49897Z"
                                            fill="#FDAE4B" stroke="black" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <div class=" flex items-center gap-x-1 pt-1">
                                        <div class="">
                                            {{ number_format($ad->customerReviews()->avg('rating'), 1) }}
                                        </div>
                                        <div class=" w-1 h-1 bg-black rounded-full"></div>
                                        <div @click="$dispatch('open-modal', {id: 'view-review'});"
                                            class="cursor-pointer font-semibold underline underline-offset-1 whitespace-nowrap">
                                            {{ $ad->customerReviews()->count() }} Reviews
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($ad->returnPolicy)
                                <div class=" flex items-center justify-between">
                                    <div class=" flex items-center gap-x-1">
                                        <div class=" w-2 h-2 bg-[#90EE90] rounded-full"></div>
                                        <div class=" text-sm">{{ $ad->returnPolicy?->policy_name }}</div>
                                    </div>
                                    <div @click="policy = true"
                                        class=" text-sm underline underline-offset-1 cursor-pointer">Refund Policy
                                    </div>
                                </div>
                            @endif

                            <div class=" pt-5">
                                @if (isECommerceAddToCardEnabled())
                                    <x-button wire:click="addToCart()" size="lg"
                                        class="w-full mb-4 bg-[#90EE90] font-semibold border-black text-black">{{ __('messages.t_add_to_cart') }}</x-button>
                                @endif
                                @if (isECommerceBuyNowEnabled())
                                    <x-button.secondary wire:click="buyNow()" size="lg"
                                        class="w-full mb-4">{{ __('messages.t_buy_now') }}</x-button.secondary>
                                @endif
                            </div>
                            <div x-show="policy"
                                class="fixed inset-0 flex items-end lg:items-center justify-center z-50 bg-black dark:bg-opacity-90 bg-opacity-50"
                                x-cloak x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-90">
                                <div @click.outside="policy = false"
                                    class="bg-white rounded-ss-xl rounded-se-xl md:rounded-xl w-[40rem] h-fit dark:bg-gray-800 dark:border-white/10 dark:border p-5">
                                    <div class="flex justify-end">
                                        <svg @click="policy = false" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class=" w-5 h-5 cursor-pointer">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <p class="text-lg font-semibold flex items-center gap-x-1">
                                        {{ $ad->returnPolicy?->policy_name }}
                                    </p>
                                    <p class="pt-5">{{ $ad->returnPolicy?->description }}</p>
                                </div>
                            </div>
                        </div>
                    @endif


                    @if (is_vehicle_rental_active())
                        @if ($ad->start_date || ($vehicleRentalSettings->enable_whatsapp && $ad->user->phone_number))
                            <div
                                class="border border-gray-200 dark:border-white/20 classic:border-black lg:border-t-0 border-l-0 border-r-0 rounded-none p-4">
                                @if ($ad->start_date)
                                    <x-ad.vehicle-booking :$ad />
                                @endif
                                <!-- Chat with Owner Button -->
                            </div>
                        @endif
                    @endif
                    {{-- @if (!is_ecommerce_active() && $whatsappSettings->enable_whatsapp && $ad->user->phone_number)
                        <div class=" p-4 border-b classic:border-black">
                            <x-button wire:click="chatWithWhatsapp()" size="lg"
                                class="w-full  gap-x-2 border-black text-black dark:bg-[#90EE90]">
                                <img src="{{ asset('/images/logos_whatsapp-icon.svg') }}" class="h-6 w-6">
                                <span>{{ __('messages.t_chat') }}</span>
                            </x-button>
                        </div>
                    @endif --}}
                    {{-- @if (!is_ecommerce_active() && $phoneSettings->enable_phone && $ad->user->phone_number)
                        <div class=" p-4 border-b classic:border-black">
                            <x-ad.phone :phoneNumber="$ad->user->phone_number" />
                        </div>
                    @endif --}}

                    @if (!is_ecommerce_active())
                        <div
                            class="hidden md:block border dark:border-white/20 classic:border-black border-l-0 border-r-0 border-t-0">
                            <x-ad.contact />
                        </div>
                    @endif

                    <div>
                        <livewire:user.seller-info :$isWebsite :$ad
                            extraClass="border-l-0 border-r-0 md:border-t-0 rounded-none" />
                    </div>

                    @if (optional(current($customizationSettings->ad_detail_page))['enable_mobile_view_ad_action_in_below_ad'])
                    <div class="md:hidden px-6 pt-6  pb-6 border-b border-gray-200 dark:border-white/20 rounded classic:border-black border-l-0 border-r-0 rounded-none">
                        <x-ad.share-report :$isFavourited :$ad />
                    </div>
                    @endif
                    @if ($externalAdSettings->enable)
                        @if (
                            !getSubscriptionSetting('status') ||
                                (getSubscriptionSetting('status') &&
                                    in_array(getUserSubscriptionPlan($ad->user_id)?->ads_level, ['basic', 'advanced'])))
                            <style>
                                .external-ad {
                                    padding-top: @php echo ($externalAdSettings->ad_top_spacing ?? 8) . 'px';
                                @endphp
                                ;
                                padding-right: @php echo ($externalAdSettings->ad_right_spacing ?? 8) . 'px';
                                @endphp
                                ;
                                padding-bottom: @php echo ($externalAdSettings->ad_bottom_spacing ?? 8) . 'px';
                                @endphp
                                ;
                                padding-left: @php echo ($externalAdSettings->ad_left_spacing ?? 8) . 'px';
                                @endphp
                                ;
                                }
                            </style>


                            <!-- external ads -->
                            <div class="external-ad w-full border-b classic:border-black ">
                                {!! $externalAdSettings->value !!}
                            </div>
                        @endif
                    @endif
                    <div class="py-6 px-4 hidden md:block">
                        <h3 class="text-lg mb-4 font-semibold">{{ __('messages.t_ad_actions') }}</h3>
                        <x-ad.share-report :$isFavourited :$ad />
                    </div>

                    @if ($tags && count($tags) > 0)
                        <div class="py-6 px-4 md:border-t dark:border-white/20 classic:border-black">
                            <h3 class="text-lg mb-4 font-semibold">{{ __('messages.t_tags') }}</h3>
                            <div>
                                @foreach ($tags as $tag)
                                    <a wire:key="tag-{{ $tag['name'] }}" href="{{ $tag['link'] }}"
                                        class="inline-block bg-gray-200 hover:bg-gray-300 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2 capitalize">{{ $tag['name'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @if (!$relatedAds->isEmpty())
            <section class=" pt-6 pb-10 md:pb-6">
                <div class="container mx-auto px-4">
                    <h2 class="text-xl md:text-2xl text-left mb-6 font-semibold">{{ __('messages.t_related_ads') }}
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-y-4  gap-x-4">
                        @foreach ($relatedAds as $ad)
                            <livewire:ad.ad-item :$ad wire:key="related-{{ $ad->id }}" lazy />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>

    <div
        class="md:hidden fixed bottom-[66px] left-0 right-0 border-t border-gray-200 bg-white dark:bg-gray-900 dark:border-white/10 classic:border-black z-10 @if (is_ecommerce_active()) px-4 @endif">
        <x-ad.contact />
    </div>

    @if ($adPlacementSettings->before_footer)
    <div class="container mx-auto px-4 flex items-center justify-center md:pb-8 pb-10 " role="complementary" aria-label="{{ __('messages.t_aria_label_footer_advertisement')}}">
        {!! $adPlacementSettings->before_footer !!}
    </div>
    @endif



    <livewire:layout.sidebar />





    {{-- Modals (Share ad) --}}
    <x-filament::modal id="share-ad" width="xl" class="z-50">

        {{-- Header --}}
        <x-slot name="heading">{{ __('messages.t_share_this_ad') }}</x-slot>

        {{-- Content --}}
        <div>
            <div class="items-center justify-center md:flex md:space-y-0 space-y-4">

                {{-- Facebook --}}
                <div class="grid items-center justify-center mx-4">
                    <a href="https://www.facebook.com/share.php?u={{ url('ad', $this->ad->slug) }}&t={{ $this->ad->title }}"
                        target="_blank"
                        class="inline-flex justify-center items-center h-12 w-12 border border-transparent rounded-full bg-[#3b5998] focus:outline-none focus:ring-0 mx-auto">
                        <svg class="h-5 w-5 fill-white" version="1.1" viewBox="0 0 512 512" width="100%"
                            xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <path
                                d="M374.244,285.825l14.105,-91.961l-88.233,0l0,-59.677c0,-25.159 12.325,-49.682 51.845,-49.682l40.116,0l0,-78.291c0,0 -36.407,-6.214 -71.213,-6.214c-72.67,0 -120.165,44.042 -120.165,123.775l0,70.089l-80.777,0l0,91.961l80.777,0l0,222.31c16.197,2.541 32.798,3.865 49.709,3.865c16.911,0 33.511,-1.324 49.708,-3.865l0,-222.31l74.128,0Z" />
                        </svg>
                    </a>
                    <span
                        class="uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_facebook') }}</span>
                </div>

                {{-- Twitter --}}
                <div class="grid items-center justify-center mx-4">
                    <a href="https://twitter.com/intent/tweet?text={{ $this->ad->title }}%20-%20{{ url('ad', $this->ad->slug) }}%20"
                        target="_blank"
                        class="inline-flex justify-center items-center h-12 w-12 border border-transparent rounded-full focus:outline-none focus:ring-0 mx-auto">
                        <svg width="48px" height="48px" viewBox="0 0 30 30" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect width="30" height="30" rx="15" fill="#3F3F46" />
                            <path d="M19.7447 7.54297H22.2748L16.7473 13.8605L23.25
                    22.4574H18.1584L14.1705 17.2435L9.60746 22.4574H7.07582L12.9881 15.7L6.75
                    7.54297H11.9708L15.5755 12.3087L19.7447 7.54297ZM18.8567 20.943H20.2587L11.209
                    8.97782H9.7046L18.8567 20.943Z" fill="white" />
                        </svg>
                    </a>
                    <span
                        class="uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_twitter') }}</span>
                </div>




                {{-- Linkedin --}}
                <div class="grid items-center justify-center mx-4">
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ url('ad', $this->ad->slug) }}&title={{ $this->ad->title }}&summary={{ $this->ad->title }}"
                        target="_blank"
                        class="inline-flex justify-center items-center h-12 w-12 border border-transparent rounded-full bg-[#0a66c2] focus:outline-none focus:ring-0 mx-auto">
                        <svg class="h-5 w-5 fill-white" version="1.1" viewBox="0 0 512 512" width="100%"
                            xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <path
                                d="M473.305,-1.353c20.88,0 37.885,16.533 37.885,36.926l0,438.251c0,20.393 -17.005,36.954 -37.885,36.954l-436.459,0c-20.839,0 -37.773,-16.561 -37.773,-36.954l0,-438.251c0,-20.393 16.934,-36.926 37.773,-36.926l436.459,0Zm-37.829,436.389l0,-134.034c0,-65.822 -14.212,-116.427 -91.12,-116.427c-36.955,0 -61.739,20.263 -71.867,39.476l-1.04,0l0,-33.411l-72.811,0l0,244.396l75.866,0l0,-120.878c0,-31.883 6.031,-62.773 45.554,-62.773c38.981,0 39.468,36.461 39.468,64.802l0,118.849l75.95,0Zm-284.489,-244.396l-76.034,0l0,244.396l76.034,0l0,-244.396Zm-37.997,-121.489c-24.395,0 -44.066,19.735 -44.066,44.047c0,24.318 19.671,44.052 44.066,44.052c24.299,0 44.026,-19.734 44.026,-44.052c0,-24.312 -19.727,-44.047 -44.026,-44.047Z"
                                style="fill-rule:nonzero;" />
                        </svg>
                    </a>
                    <span
                        class="uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_linkedin') }}</span>
                </div>

                {{-- Whatsapp --}}
                <div class="grid items-center justify-center mx-4">
                    <a href="https://api.whatsapp.com/send?text={{ $this->ad->title }}%20{{ url('ad', $this->ad->slug) }}"
                        target="_blank"
                        class="inline-flex justify-center items-center h-12 w-12 border border-transparent rounded-full bg-[#25d366] focus:outline-none focus:ring-0 mx-auto">
                        <svg class="h-5 w-5 fill-white" version="1.1" viewBox="0 0 512 512" width="100%"
                            xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <path
                                d="M373.295,307.064c-6.37,-3.188 -37.687,-18.596 -43.526,-20.724c-5.838,-2.126 -10.084,-3.187 -14.331,3.188c-4.246,6.376 -16.454,20.725 -20.17,24.976c-3.715,4.251 -7.431,4.785 -13.8,1.594c-6.37,-3.187 -26.895,-9.913 -51.225,-31.616c-18.935,-16.89 -31.72,-37.749 -35.435,-44.126c-3.716,-6.377 -0.397,-9.824 2.792,-13c2.867,-2.854 6.371,-7.44 9.555,-11.16c3.186,-3.718 4.247,-6.377 6.37,-10.626c2.123,-4.252 1.062,-7.971 -0.532,-11.159c-1.591,-3.188 -14.33,-34.542 -19.638,-47.298c-5.171,-12.419 -10.422,-10.737 -14.332,-10.934c-3.711,-0.184 -7.963,-0.223 -12.208,-0.223c-4.246,0 -11.148,1.594 -16.987,7.969c-5.838,6.377 -22.293,21.789 -22.293,53.14c0,31.355 22.824,61.642 26.009,65.894c3.185,4.252 44.916,68.59 108.816,96.181c15.196,6.564 27.062,10.483 36.312,13.418c15.259,4.849 29.145,4.165 40.121,2.524c12.238,-1.827 37.686,-15.408 42.995,-30.286c5.307,-14.882 5.307,-27.635 3.715,-30.292c-1.592,-2.657 -5.838,-4.251 -12.208,-7.44m-116.224,158.693l-0.086,0c-38.022,-0.015 -75.313,-10.23 -107.845,-29.535l-7.738,-4.592l-80.194,21.037l21.405,-78.19l-5.037,-8.017c-21.211,-33.735 -32.414,-72.726 -32.397,-112.763c0.047,-116.825 95.1,-211.87 211.976,-211.87c56.595,0.019 109.795,22.088 149.801,62.139c40.005,40.05 62.023,93.286 62.001,149.902c-0.048,116.834 -95.1,211.889 -211.886,211.889m180.332,-392.224c-48.131,-48.186 -112.138,-74.735 -180.335,-74.763c-140.514,0 -254.875,114.354 -254.932,254.911c-0.018,44.932 11.72,88.786 34.03,127.448l-36.166,132.102l135.141,-35.45c37.236,20.31 79.159,31.015 121.826,31.029l0.105,0c140.499,0 254.87,-114.366 254.928,-254.925c0.026,-68.117 -26.467,-132.166 -74.597,-180.352"
                                id="WhatsApp-Logo" />
                        </svg>
                    </a>
                    <span
                        class="uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_whatsapp') }}</span>
                </div>

                {{-- Copy link --}}
                <div class="grid items-center justify-center mx-4">
                    <button type="button" x-on:click="copy" aria-label="{{__('messages.t_aria_label_copy_link')}}"
                        class="inline-flex justify-center items-center h-12 w-12 border border-transparent rounded-full bg-gray-400 focus:outline-none focus:ring-0 mx-auto">
                        <svg aria-hidden="true" class="h-5 w-5 fill-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <title />
                            <path
                                d="M17.3,13.35a1,1,0,0,1-.7-.29,1,1,0,0,1,0-1.41l2.12-2.12a2,2,0,0,0,0-2.83L17.3,5.28a2.06,2.06,0,0,0-2.83,0L12.35,7.4A1,1,0,0,1,10.94,6l2.12-2.12a4.1,4.1,0,0,1,5.66,0l1.41,1.41a4,4,0,0,1,0,5.66L18,13.06A1,1,0,0,1,17.3,13.35Z" />
                            <path
                                d="M8.11,21.3a4,4,0,0,1-2.83-1.17L3.87,18.72a4,4,0,0,1,0-5.66L6,10.94A1,1,0,0,1,7.4,12.35L5.28,14.47a2,2,0,0,0,0,2.83L6.7,18.72a2.06,2.06,0,0,0,2.83,0l2.12-2.12A1,1,0,1,1,13.06,18l-2.12,2.12A4,4,0,0,1,8.11,21.3Z" />
                            <path
                                d="M8.82,16.18a1,1,0,0,1-.71-.29,1,1,0,0,1,0-1.42l6.37-6.36a1,1,0,0,1,1.41,0,1,1,0,0,1,0,1.42L9.52,15.89A1,1,0,0,1,8.82,16.18Z" />
                        </svg>
                    </button>
                    <template x-if="!isCopied">
                        <span
                            class="uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_copy_link') }}</span>
                    </template>
                    <template x-if="isCopied">
                        <span
                            class="uppercase font-normal text-xs text-green-500 mt-4 tracking-widest">{{ __('messages.t_copied') }}</span>
                    </template>
                </div>

            </div>
        </div>

    </x-filament::modal>

    {{-- Modals (Report ad) --}}
    <x-filament::modal id="report-ad" width="xl">
        {{-- Header --}}
        <x-slot name="heading">{{ __('messages.t_report_this_ad') }}</x-slot>
        <div>
            <form wire:submit="reportAd">
                {{ $this->form }}

                <div class="mt-4">
                    <x-filament::button type="submit">
                        {{ __('messages.t_report_ad') }}
                    </x-filament::button>
                </div>
            </form>
        </div>
    </x-filament::modal>
    <div class="z-30">
        <livewire:layout.bottom-navigation />
    </div>
    <livewire:layout.footer />

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let startDate = new Date();
            let elapsedTime = 0;

            const focus = function() {
                startDate = new Date();
            };

            const blur = function() {
                const endDate = new Date();
                const spentTime = endDate.getTime() - startDate.getTime();
                elapsedTime += spentTime;
            };

            const beforeunload = function() {
                const endDate = new Date();
                const spentTime = endDate.getTime() - startDate.getTime();
                elapsedTime += spentTime;
                const timeSpentInSeconds = Math.round(elapsedTime / 1000);
                Livewire.dispatch('saveTimeSpend', {
                    'timeSpentInSeconds': timeSpentInSeconds
                });
                // elapsedTime contains the time spent on page in milliseconds
            };

            window.addEventListener('focus', focus);
            window.addEventListener('blur', blur);
            window.addEventListener('beforeunload', beforeunload);

        });
    </script>
</div>
