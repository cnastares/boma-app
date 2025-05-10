<div class="relative">
    @php
    $isAdType = (count($adTypes) > 1);
    @endphp

    @if(app('filament')->hasPlugin('appearance'))
    <style>
        @media (min-width: 1024px) {
            .category-bar {
                padding-top: {
                        {
                        $homeSettings->header_between_line_spacing
                    }
                }

                px !important;
            }
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
    @endif
    @if($adTypes->isNotEmpty())
    <nav aria-label="{{__('messages.t_aria_label_ad_type_navigation') }}" class="category-bar pt-6 overflow-x-auto hide-scrollbar" x-data="{ context: @entangle('context') }">

        <div class="flex md:space-x-4 whitespace-nowrap items-center "
        x-data="{
            scroll: null,
            isScrollStart: true,
            isScrollEnd: false,
            init() {
                this.scroll = this.$refs.adTypeScrollContainer;

                // Wait until the scroll container has a valid width and height
                if (this.scroll.clientWidth > 0 && this.scroll.clientHeight > 0) {
                    this.updateScrollButtons();
                } else {
                    // Recheck until dimensions are valid using requestAnimationFrame
                    requestAnimationFrame(() => this.init());
                }

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
        }" >



        @if($homeSettings->show_all_category)
        <div class="hidden md:block group">
            <a href="#" style="font-size: {{$homeSettings->all_category_font_size}}px"
                class="flex gap-x-1 items-center  font-bold rounded-full py-2 w-24 md:w-auto !-outline-offset-1">
                {{ __('messages.t_all') }}
                <x-heroicon-o-chevron-down
                    class="w-6 h-6 transform {{$homeSettings->show_all_category_animation?'group-hover:rotate-180':''}}  transition-transform dark:text-gray-500" />
            </a>
            @if ($homeSettings->show_all_category_animation)
            <div
                class="mega-group-container  md:group-hover:block hidden absolute left-0 right-0 z-20 mt-0 overflow-auto h-[calc(100vh - 50rem)] bg-white shadow-lg rounded-xl ring-1 ring-gray-950/5  dark:bg-gray-900 dark:ring-white/10 classic:ring-black  classic:group-hover:shadow-custom  md:group-focus-within:block">
                <div class="flex mt-0">
                    @if ($isAdType)
                    <div class="cursor-pointer min-w-[20%] border-[#d1d5d8] classic:border-black border-r">
                        @foreach($adTypes as $adType)
                        <button type="button" class="focus-within:outline focus-within:-outline-offset-2 hover:bg-[#F1F1F1] {{ $selectedAdType?->id == $adType?->id ? 'bg-[#F1F1F1]' : '' }} p-3 flex justify-between items-center gap-x-7 cursor-pointer w-full !-outline-offset-4"
                            wire:click="selectAdType('{{$adType?->id}}')">
                            <div wire:key="ad-type-md-{{ $adType?->id }} mb-3">
                                <h4 class="whitespace-normal break-all">{{ $adType?->name }}</h4>
                            </div>
                            <div>
                                <x-icon-arrow-right class="w-5 h-3 dark:text-gray-400 cursor-pointer" />
                            </div>
                        </button>
                        @endforeach
                    </div>
                    @endif
                    <div class="w-full">
                        <div class="grid grid-cols-4 gap-4 {{ $isAdType ?'p-10' :'p-4'}}">
                            @forelse($categories as $category)
                            <div wire:key="category-md-{{ $category->id }}">
                                <a href="{{generate_category_url($category->adType, $category) }}"
                                    class="font-bold mb-5 inline-block truncate  break-words w-full">{{ $category->name }}</a>
                                <ul>
                                    @foreach($category->subcategories as $subcategory)
                                    <li wire:key="subcategory-md-{{ $subcategory->id }}" class="py-0.5">
                                        <a href="{{generate_category_url($category->adType, $category, $subcategory) }}"
                                            class="text-sm hover:underline inline-block truncate  break-words w-full">
                                            {{ $subcategory->name }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @empty
                           {{ __('messages.t_no_categories')}}
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
        <button type="button" x-show="!isScrollStart" aria-label="{{__('messages.t_aria_label_previous')}}"
        class=" bg-white/80 py-1 " @click="scrollLeft" >
        <x-heroicon-o-chevron-left aria-hidden="true" x-tooltip="{
                            content: '{{__('messages.t_tooltip_previous')}}',
                            theme: $store.theme,
                        }"
            class="h-6 w-6 text-black p-0.5 cursor-pointer classic:mb-1" />
        </button>
            <ul
                x-ref="adTypeScrollContainer"
                class="flex py-1  pr-3 overflow-x-auto flex-grow md:gap-4 no-scrollbar  gap-4  max-md:overflow-x-auto relative">
            @if ($isAdType)
                    @foreach($adTypes as $adType)
                    <li class="group">
                        <a draggable="false" href="{{generate_category_url($adType, null)}}"
                            class="block md:text-sm md:py-1 md:px-3  border border-transparent dark:border-gray-900 md:rounded-full  md:group-hover:border-black dark:group-hover:border-white/10 transition-all md:group-hover:transform md:group-hover:-translate-x-1 md:group-hover:-translate-y-1 classic:border-b-4 classic:border-r-4 classic:border-transparent classic:border-black w-24 md:w-auto mx-1"
                            wire:navigate>
                            <img src="{{ $adType?->icon }}" alt="{{ $adType?->name }}"
                                class="h-12 w-12 md:w-20 md:h-20 pb-3 mx-auto  md:hidden">
                            <span
                                class="text-xs md:text-sm line-clamp-1 whitespace-normal md:line-clamp-none md:whitespace-nowrap text-center uppercase md:capitalize">{{
                                $adType?->name }}</span>
                        </a>
                    </li>
                    @endforeach
            @else
            @foreach($categories as $category)
            <li class="group">
                <a draggable="false" href="{{generate_category_url($category->adType, $category) }}"
                    class="block md:text-sm md:py-1 md:px-3  border border-transparent dark:border-gray-900 md:rounded-full  md:group-hover:border-black dark:group-hover:border-white/10 transition-all md:group-hover:transform md:group-hover:-translate-x-1 md:group-hover:-translate-y-1 classic:border-b-4 classic:border-r-4 classic:border-transparent classic:border-black w-24 md:w-auto mx-1"
                    >
                    <img src="{{ $category->icon }}" alt="{{ $category->name }}"
                        class="h-12 w-12 md:w-20 md:h-20 pb-3 mx-auto  md:hidden">
                    <span
                        class="text-xs md:text-sm line-clamp-1 whitespace-normal md:line-clamp-none md:whitespace-nowrap text-center uppercase md:capitalize">{{
                        $category->name }}</span>
                </a>
            </li>
            @endforeach
            @endif
            </ul>

            <button type="button" x-show="!isScrollEnd" aria-label="{{__('messages.t_aria_label_next')}}"
            class=" bg-white/80 py-1  " @click="scrollRight" >
            <x-heroicon-o-chevron-right aria-hidden="true" x-tooltip="{
                content: '{{__('messages.t_tooltip_next')}}',
                theme: $store.theme,
                }"
                class="h-6 w-6 text-black  p-0.5 cursor-pointer classic:mb-1" />
            </button>
        </div>
    </nav>
    @endif
    @script
    <script>
            const scrollContainer = document.querySelector('[x-ref="adTypeScrollContainer"]');
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
