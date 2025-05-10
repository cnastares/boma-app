@assets
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" async data-navigate-track ></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endassets
<div role="group" wire:ignore
    class="swiper mySwiper h-full">
    <div class="swiper-wrapper ">
        @foreach ($banners as $key=>$banner)
        <div wire:key='banner-{{$banner->id}}' class="swiper-slide rounded-lg  text-center"
             data-banner-id="{{ $banner->id }}" wire:click='updateClickCount({{$banner->id}})'>
            {!! $banner->bannerImage->img('',
            ['alt'=>$banner->alternative_text,'class'=>'w-full h-full bg-center']) !!}
        </div>
        @endforeach
    </div>
    <button type="button" aria-label="{{__('messages.t_aria_label_next_item')}}"
    x-data x-tooltip="{
        content: '{{__('messages.t_tooltip_next')}}',
        theme: $store.theme,
    }"
        class="swiper-button-next banner-carousel-side-buttons"></button>

    <button type="button" aria-label="previous list item"
    x-data x-tooltip="{
        content: '{{__('messages.t_tooltip_previous')}}',
        theme: $store.theme,
    }"
        class="swiper-button-prev banner-carousel-side-buttons"></button>
    {{-- @if ($this->bannerSettings->enable_autoplay)
    <button
        class="absolute right-3 bottom-3 z-10 text-black bg-white rounded cursor-pointer apply-themes w-6 h-6 font-bold"
        onclick="updateautoPlayStatus()" aria-label="pause and play button">
        <x-heroicon-o-pause x-cloak id="stopAutoPlay" />
        <x-heroicon-o-play x-cloak id="startAutoPlay" />
    </button>
    @endif --}}
</div>
{{-- <div wire:ignore
    class="mx-auto w-fit mt-2  h-[6%] !max-w-[90%]  without-pagination-count ">
    <div class="swiper-pagination-custom !flex  rounded transition-all !overflow-x-auto  p-1"></div>
</div> --}}
<script data-navigate-once>

    //Define bannerSettings
    const bannerSettings = {
        autoplay: false,
        enablePaginationCount: false
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
            return '<button type="button" class="' + className + '">' + (index + 1)+'/' +itemCount+"</button>";
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
