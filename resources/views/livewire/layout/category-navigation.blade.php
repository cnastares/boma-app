<div class="relative">
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

        /* To set scroll bar width */
        .scroll-width::-webkit-scrollbar {
            width: 3px;
            height: 3px;
        }


        .scroll-width::-webkit-scrollbar-track {
            background: #f1f1f1;
        }


        .scroll-width::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }


        .scroll-width::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    @endif
    @if($categories->isNotEmpty())
    <nav class="category-bar pt-6 overflow-x-auto scroll-width" x-data="{ context: @entangle('context') }">
        <ul class="flex md:space-x-4 whitespace-nowrap items-center">
            @if($homeSettings->show_all_category)
            <li class="hidden md:block group">
                <a href="#" style="font-size: {{$homeSettings->all_category_font_size}}px"
                    class="flex gap-x-1 items-center  font-bold rounded-full py-2 w-24 md:w-auto">
                    {{ __('messages.t_all') }}
                    <x-heroicon-o-chevron-down
                        class="w-6 h-6 transform group-hover:rotate-180 transition-transform dark:text-gray-500" />
                </a>
                <div
                    class="mega-group-container md:group-hover:block hidden absolute left-0 right-0 z-10 mt-0 overflow-auto h-[calc(100vh - 50rem)] bg-white shadow-lg py-2 rounded-xl ring-1 ring-gray-950/5  dark:bg-gray-900 dark:ring-white/10 classic:ring-black  {{$homeSettings->enable_hover_animation?'classic:group-hover:shadow-custom':''}}">
                    <div class="grid grid-cols-4 gap-4 p-4">
                        @foreach($categories as $category)
                        <div wire:key="category-md-{{ $category->id }}">
                            <h4 class="font-bold">{{ $category->name }}</h4>
                            <ul>
                                @foreach($category->subcategories as $subcategory)
                                <li wire:key="subcategory-md-{{ $subcategory->id }}">
                                    <a href="{{ generate_category_url($category->adType, $category, $subcategory, $locationSlug) }}"
                                        class="text-sm hover:underline">
                                        {{ $subcategory->name }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                </div>
            </li>
            @endif
            @foreach($categories as $category)
            <livewire:layout.category-navigation-item wire:key="category-nav-{{ $category->id }}" :$locationSlug
                :$category />
            @endforeach
        </ul>
    </nav>
    @endif
</div>
