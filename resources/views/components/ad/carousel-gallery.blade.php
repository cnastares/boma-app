@props(['images' => [], 'videoLink' => null, 'image_properties' => null, "ad_title" => null, 'ad'])
   <!-- Main Image -->

@assets
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" async data-navigate-track ></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endassets

@php
$customizationSettings = optional(current($customizationSettings->ad_detail_page));
$images = (count($images) > 0) ? $images : [getAdPlaceholderImage($ad->id)];
@endphp

<div class="w-full border-b dark:border-white/20 classic:border-black" >

    {{-- <style>
        .swiper-button-next::after {
            font-size: 18px;
            font-weight: bold;
        }

        .swiper-button-prev::after {
            font-size: 18px;
            font-weight: bold;
        }
    </style> --}}

    <div role="group" wire:ignore class="swiper mySwiper md:h-[25rem] h-[10rem] md:rounded-ss-xl !bg-black">
    <div class="swiper-wrapper md:rounded-ss-xl" >
            @foreach ($images as $key=>$banner)
            <div wire:key='ad-image-{{$key}}' class="swiper-slide md:rounded-ss-xl"
                data-banner-id="{{ 'ad-image-' . $key }}">
                <img src="{{$banner}}" id="{{ 'ad-images-' . $key+1 }}"
                    class=" cursor-pointer w-full h-full object-contain object-center"
                    alt="{{$banner->alternative_text??$ad_title .__('messages.t_alt_image')}}">
            </div>
            @endforeach
        </div>

        <!-- Navigation Buttons -->
        <button type="button" aria-label="{{ __('messages.t_aria_label_previous_item') }}"
            x-data x-tooltip="{
                content: '{{__('messages.t_tooltip_previous')}}',
                theme: $store.theme,
            }"
            class="swiper-button-prev banner-carousel-side-buttons !hidden md:!block "></button>

        <button type="button" aria-label="{{ __('messages.t_aria_label_next_item') }}"
            x-data x-tooltip="{
                content: '{{__('messages.t_tooltip_next')}}',
                theme: $store.theme,
            }"
            class="swiper-button-next banner-carousel-side-buttons !hidden md:!block "></button>

        <!-- Autoplay Buttons-->
        @if ($customizationSettings['enable_autoplay'])
        <button
        x-cloak
        id="autoplayButton"
        class="absolute right-3 bottom-3 z-10 text-black bg-white bg-opacity-60 focus:bg-opacity-95 rounded cursor-pointer apply-themes w-6 h-6 font-bold"
        x-data="{ isPlaying: {{ $customizationSettings['enable_autoplay'] ? 'true' : 'false' }} }"
        @click="isPlaying = !isPlaying; updateautoPlayStatus()"
        :aria-label="isPlaying ? '{{ __('messages.t_aria_label_pause') }}' : '{{ __('messages.t_aria_label_play') }}'"
        x-tooltip="{
            content: isPlaying ? '{{ __('messages.t_tooltip_pause') }}' : '{{ __('messages.t_tooltip_play') }}',
            theme: $store.theme,
        }"
        >
            <template x-if="isPlaying">
                <x-heroicon-o-pause />
            </template>
            <template x-if="!isPlaying">
                <x-heroicon-o-play />
            </template>
        </button>
        @endif

    </div>
    @if (count($images) > 1)
        <div wire:ignore
            class="mx-auto w-fit my-2  h-[6%] !max-w-[90%]  {{$customizationSettings['enable_pagination_count']?'with-pagination-count':'without-pagination-count'}} ">
            <div class="swiper-pagination-custom !flex  rounded transition-all !overflow-x-auto  p-1"></div>
        </div>
    @endif

    <script>
        //Define bannerSettings
        const bannerSettings = {
            autoplay: {{ $customizationSettings['enable_autoplay'] ? 'true' : 'false' }},
            autoplayInterval: {{ $customizationSettings['autoplay_interval'] ?? 1500 }},
            enablePaginationCount:{{ $customizationSettings['enable_pagination_count'] ? 'true' : 'false' }}
        };

        var stopButton=document.getElementById('stopAutoPlay');
        var startButton=document.getElementById('startAutoPlay');

        //Define Swiper options
        const swiperOptions={
            loop: true,
            pagination: {
                el: ".swiper-pagination-custom",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            lazy: {
            loadPrevNext: true, // pre-loads the next image to avoid showing a loading placeholder if possible
            loadPrevNextAmount: 2 //or, if you wish, preload the next 2 images
        },
        };

        //Define autoplay options based on the condition
        if(bannerSettings.autoplay){
            swiperOptions.autoplay={
                delay: bannerSettings.autoplayInterval,
                disableOnInteraction: false
            }
        }

        if(bannerSettings.enablePaginationCount){
            swiperOptions.pagination.renderBullet=function (index, className) {
                const itemCount=document.querySelectorAll('.swiper-slide').length;
                const id="ad-images-"+(index+1);

                const image=document.getElementById(id);
                const alt = image.alt ? image.alt.replace(/'/g, "\\'") : null;
                const bannerText=`{{__('messages.t_tooltip_banner')}} ${(index+1)}`;
                const tooltip=`x-tooltip="{
                    content: '${alt ? alt : bannerText }',
                    theme: $store.theme,
                }"`;
                const button='<button '+ tooltip +' type="button" class="' + className + '">' + (index + 1)+'/' +itemCount+"</button>";

                return button;
            }
        }
        var swiper;
        document.addEventListener('livewire:navigated', () => {
        swiper = new Swiper(".mySwiper", swiperOptions);
            if(swiper){
                const autoPlayStatus=swiper.autoplay.running;
                waitForElement("#stopAutoPlay", () => {
                    updateautoPlayButtons(startButton,stopButton,autoPlayStatus);
                });
            }
        })
        function updateautoPlayButtons(startButton,stopButton,autoPlayStatus){
            if(autoPlayStatus){
                startButton.style.display='none';
                stopButton.style.display='block';
            }else{
                startButton.style.display='block';
                stopButton.style.display='none';
            }
        }
        function updateautoPlayStatus(){
            const autoPlayStatus=!swiper.autoplay.running;
            autoPlayStatus?swiper.autoplay.start():swiper.autoplay.stop();
            updateautoPlayButtons(startButton,stopButton,autoPlayStatus);
        }
        // Intersection Observer to track when a banner is visible
        const options = {
                root: null,
                rootMargin: '0px',
                threshold: 0.5
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const bannerId = entry.target.getAttribute('data-banner-id');
                        Livewire.dispatch('update-banner-view', {bannerId:bannerId});
                    }
                });
            }, options);
        document.addEventListener('livewire:initialized', () => {

        document.querySelectorAll('.swiper-slide').forEach(slide => {
                observer.observe(slide);
            });
        });
        function waitForElement(selector, callback) {
            const intervalId = setInterval(() => {
            if (document.querySelector(selector)) {
                clearInterval(intervalId);
                callback();
            }
            }, 500);
        }
    </script>
    @if ($customizationSettings['enable_price_below_image'] && ($ad->adType?->disable_price_type == false || in_array($ad->adType?->marketplace,  [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE])))
    <x-custom-price
        value="{{ formatPriceWithCurrency($ad->price) }}"
        type_id="{{ in_array($ad->adType?->marketplace,  [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]) ? 1 : $ad->price_type_id }}" label="{{ $ad->priceType->label }}"
        has_prefix="{{ $ad?->adType?->has_price_suffix }}"
        price_suffix="{{ $ad->price_suffix }}" offer_enabled="{{ $ad->isEnabledOffer() }}"
        offer_price="{{ $ad->offer_price ? formatPriceWithCurrency($ad->offer_price) : null }}"
        offer_percentage="{{ $ad->getOfferPercentage() }}" />
    @endif
</div>
