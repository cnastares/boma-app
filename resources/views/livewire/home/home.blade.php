<div x-data class="pb-14 md:pb-0">
    <!-- Skip links -->
    @include('components.skip-links',['links'=>[
        'main-content'=> __('messages.t_skip_to_main_content'),
        'footer'=> __('messages.t_skip_to_footer')
    ]])

    <livewire:layout.header context="home" lazy />

    <livewire:home.banner />

    @if ($adPlacementSettings->after_header)
    <div class="container mx-auto px-4 flex items-center justify-center md:pt-8 pt-6" role="complementary" aria-label="{{ __('messages.t_aria_label_header_advertisement')}}">
        {!! $adPlacementSettings->after_header !!}
    </div>
    @endif

    <!-- Main content -->
    <main id="main-content" class="sticky-scroll-margin" wire:ignore.self >

    @foreach ($this->homeSettings->section_data as $item)
    @switch($item['type']??'')
    @case('categories')

    <section class="py-4 mt-4 md:py-8" aria-labelledby="categories">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row md:gap-10 justify-between">
                <h2 id="categories" class="text-xl md:text-2xl rtl:text-right ltr:text-left mb-6 font-semibold whitespace-nowrap">
                    {{ __('messages.t_explore_by_categories') }}
                </h2>
                @if (!$adTypes->isEmpty() && count($adTypes) > 1)
                @if ($homeSettings->ad_type_dropdown_enable)
                <div class="flex items-center gap-5 mb-10 p-1 overflow-x-auto hide-scroll ">
                    <x-filament::input.wrapper class="w-full">
                        <x-filament::input.select class="w-full" wire:model.live="selected_ad_type" id="sort-by">
                            @foreach ($adTypes as $index => $adType)
                            <option value="{{$adType?->id}}">{{ $adType?->name }}</option>
                            <!-- :active="$activeTab === 'tab'.$index + 1" -->
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
                @else
                <div class="flex items-center gap-5 mb-10 overflow-x-auto hide-scroll">
                    @foreach ($adTypes as $index => $adType)
                    <button wire:click="loadCategory('{{$adType?->id}}', 'tab{{$index + 1}}')" class="text-xs md:text-sm  {{$activeTab === 'tab'.$index + 1 ? 'block text-white py-1.5 px-4 rounded-xl bg-black dark:bg-primary-600 dark:text-white whitespace-nowrap' : 'block border border-[#d1d5d8] classic:border-black text-black py-1.5 px-4 rounded-xl bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:border-black dark:text-white whitespace-nowrap'}} ">
                        {{ $adType?->name }}
                    </button>
                    <!-- :active="$activeTab === 'tab'.$index + 1" -->
                    @endforeach
                </div>
                @endif
                @endif
            </div>
            <div class="relative" x-data="{
                                scroll: null,
                                isScrollStart: true,
                                isScrollEnd: false,
                                init() {
                                    this.scroll = this.$refs.scrollContainer;
                                    this.updateScrollButtons();
                                    this.scroll.addEventListener('scroll', () => this.updateScrollButtons());
                                },
                                updateScrollButtons() {
                                    this.isScrollStart = this.scroll.scrollLeft <= 0;
                                    this.isScrollEnd = this.scroll.scrollLeft + this.scroll.clientWidth >= this.scroll.scrollWidth;
                                },
                                scrollLeft() {
                                    this.scroll.scrollBy({ left: -200, behavior: 'smooth' });
                                },
                                scrollRight() {
                                    this.scroll.scrollBy({ left: 200, behavior: 'smooth' });
                                }
                            }">
                <!-- Scroll left -->
                @if (getCurrentTheme() == 'modern')
                <div x-show="!isScrollStart" class="absolute top-1/2 z-10 left-0 transform -translate-y-1/2 flex items-center gap-x-2">
                    <x-heroicon-o-chevron-left
                        x-tooltip="{
                                        content: '{{__('messages.t_tooltip_previous')}}',
                                        theme: $store.theme,
                                    }"
                        @click="scrollLeft" class="h-6 w-6 text-white bg-primary-600 rounded-full p-0.5 cursor-pointer" />
                </div>
                @endif

                @if ($categories->isNotEmpty())
                <div
                    x-ref="scrollContainer"
                    class="flex py-1 pl-1 pr-3 {{getCurrentTheme() == 'classic'?'md:p-0 md:flex-none md:grid md:grid-cols-2 md:gap-8':' overflow-x-auto flex-grow md:gap-4 no-scrollbar'}}  gap-4  max-md:overflow-x-auto">
                    @foreach ($categories as $category)
                    <livewire:home.category-card :$category wire:key="category-{{ $category->id }}" />
                    @endforeach
                </div>
                @else
                <div class="flex flex-col items-center justify-center p-5 w-full">
                    <x-not-found sizes='sm' description="No categories available for the selected filter." />
                </div>
                @endif
                <!-- Scroll right -->
                @if (getCurrentTheme() == 'modern')
                <div x-show="!isScrollEnd" class="absolute top-1/2 right-0 transform -translate-y-1/2 flex items-center gap-x-2">
                    <x-heroicon-o-chevron-right
                        x-tooltip="{
                                        content: '{{__('messages.t_tooltip_next')}}',
                                        theme: $store.theme,
                                    }"
                        @click="scrollRight" class="h-6 w-6 text-white bg-primary-600 rounded-full p-0.5 cursor-pointer" />
                </div>
                @endif

            </div>
        </div>
    </section>
    @break

    @case('spotlight')
    @if (!$spotlightAds->isEmpty())
    <section class="py-4 md:py-8" aria-labelledby="spotlight">
        <div class="container mx-auto px-4">
            <h2 id="spotlight" class="text-xl md:text-2xl rtl:text-right ltr:text-left mb-6 font-semibold">
                {{ __('messages.t_spotlight_display') }}
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5  gap-4   sm:gap-x-4">
                @foreach ($spotlightAds as $ad)
                <livewire:ad.ad-item :$ad wire:key="ad-{{ $ad->id }}" :isSpotlight="true" lazy />
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @break

    @case('fresh_ads')
    @if (!$freshAds->isEmpty())
    <section class="py-4 pb-10 md:py-8 " aria-labelledby="fresh_ads">
        <div class="container mx-auto px-4">
            <h2 id="fresh_ads" class="text-xl md:text-2xl rtl:text-right ltr:text-left mb-6 font-semibold">{{ __('messages.t_fresh_recommend') }}
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5  gap-4  sm:gap-x-4 ">
                @foreach ($freshAds as $ad)
                <livewire:ad.ad-item :$ad wire:key="fresh-ad-{{ $ad->id }}" lazy />
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @break

    @default
    @endswitch
    @endforeach

    @if (count($popularAds) && getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()?->product_performance_analysis)
    <section class="py-4 pb-10 md:py-8 " aria-labelledby="popular_ads">
        <div class="container mx-auto px-4">
            <h2 id="popular_ads" class="text-xl md:text-2xl rtl:text-right ltr:text-left mb-6 font-semibold">{{ __('messages.t_popular_ads') }}
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5  gap-4  sm:gap-x-4 ">
                @foreach ($popularAds as $ad)
                <livewire:ad.ad-item :$ad wire:key="popular-ad-{{ $ad->id }}" lazy />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @foreach ($displayedPopularAdsCategories as $categoryId)
    @if (count($this->getPopularAdsBasedOnCategory($categoryId)))
    <section class="py-4 pb-10 md:py-8 " aria-labelledby="popular_in_category">
        <div class="container mx-auto px-4">
            @php
            $categoryName = \App\Models\Category::find($categoryId)?->name ?? '';
            @endphp
            <h2 id="popular_in_category" class="text-xl md:text-2xl rtl:text-right ltr:text-left mb-6 font-semibold">{{__('messages.t_popular_in_category',['category'=>$categoryName ?? ''])}}
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5  gap-4  sm:gap-x-4 ">
                @foreach ($this->getPopularAdsBasedOnCategory($categoryId) as $ad)
                <livewire:ad.ad-item :$ad wire:key="fresh-ad-{{ $ad->id }}" lazy />
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @endforeach

    </main>

    @if ($adPlacementSettings->before_footer)
    <div class="container mx-auto px-4 flex items-center justify-center md:pb-10 pb-8" role="complementary" aria-label="{{ __('messages.t_aria_label_footer_advertisement')}}">
        {!! $adPlacementSettings->before_footer !!}
    </div>
    @endif

    <livewire:layout.sidebar />

    <livewire:layout.footer />

    <nav aria-label="{{__('messages.t_aria_label_post_ad')}}">
    <a href="/post-ad"
        class="z-10 bg-gray-900 dark:bg-primary-600 dark:text-black  text-white py-2 px-4 rounded-full fixed bottom-20 right-4 mb-1 md:hidden flex items-center justify-center gap-x-1"
        wire:navigate>
        <span>
            <x-heroicon-o-plus class="w-4 h-4" />
        </span> {{ __('messages.t_post_your_ad') }}
    </a>
    </nav>
    <livewire:layout.bottom-navigation />

    @script
    <script>
            const scrollContainer = document.querySelector('[x-ref="scrollContainer"]');
            let isDragging = false;
            let startX, scrollLeft;

            // Mouse Down (Start Dragging)
            scrollContainer.addEventListener('mousedown', (e) => {
                isDragging = true;
                scrollContainer.classList.add('dragging');
                startX = e.pageX - scrollContainer.offsetLeft;
                scrollLeft = scrollContainer.scrollLeft;
            });

            // Mouse Move (Dragging)
            scrollContainer.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX - scrollContainer.offsetLeft;
                const walk = (x - startX) * 2; // Adjust scroll speed
                scrollContainer.scrollLeft = scrollLeft - walk;
            });

            // Mouse Up / Leave (Stop Dragging)
            scrollContainer.addEventListener('mouseup', () => {
                isDragging = false;
                scrollContainer.classList.remove('dragging');
            });

            scrollContainer.addEventListener('mouseleave', () => {
                isDragging = false;
                scrollContainer.classList.remove('dragging');
            });

            // Optional: Add touch support for mobile
            scrollContainer.addEventListener('touchstart', (e) => {
                startX = e.touches[0].pageX - scrollContainer.offsetLeft;
                scrollLeft = scrollContainer.scrollLeft;
            });

            scrollContainer.addEventListener('touchmove', (e) => {
                const x = e.touches[0].pageX - scrollContainer.offsetLeft;
                const walk = (x - startX) * 2; // Adjust scroll speed
                scrollContainer.scrollLeft = scrollLeft - walk;
            });
    </script>
    @endscript
</div>
