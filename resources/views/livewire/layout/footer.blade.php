<footer class="hidden md:block " id="footer">
    @if(!auth()->user())
    <div class="bg-black py-5 dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
        <div class="container mx-auto py-10">
            <div class="md:flex flex-col md:items-center space-y-5">
                <p class="text-white text-2xl font-bold">{{ __('messages.t_post_connect_deal') }}</p>

                <p class="text-white text-lg">
                    {{ __('messages.t_post_ad_content', ['siteName' => $generalSettings->site_name]) }}
                </p>
                <a href="/post-ad"
                    class="bg-primary-600 text-black flex gap-x-1.5 justify-center items-center px-6 py-2 cursor-pointer rounded-xl">
                    <span class="text-lg font-medium">{{ __('messages.t_post_first_ad') }}</span>
                    <x-icon-arrow-right  class="rtl:rotate-180 ltr:rotate-0"/>
                </a>
            </div>
        </div>
    </div>
    @endif
    <div class="bg-white pt-14 pb-8 dark:bg-gray-950 border-t border-gray-200 classic:border-black dark:border-white/10">

        <div class="container mx-auto px-4">
            <div class="flex gap-8">
                @foreach($footerSections as $section)
                <div wire:key='footer-section-{{$section->id}}' class="{{ $loop->first ? 'w-2/5' : 'w-1/5' }}">
                        @if($section->predefined_identifier === 'site_with_social')
                        <div class="space-y-8">
                                    <span x-data x-tooltip="{
                                        content: '{{__('messages.t_tooltip_logo')}}',
                                        theme: $store.theme,
                                    }">
                                        <x-brand />
                                    </span>
                                    <p class="leading-6 ">
                                        {!! $generalSettings->site_description !!}
                                    </p>
                                    <x-social-media :facebook_link="$socialSettings->facebook_link" :twitter_link="$socialSettings->twitter_link" :linkedin_link="$socialSettings->linkedin_link" :instagram_link="$socialSettings->instagram_link" />
                        </div>
                        @elseif($section->predefined_identifier === 'popular_category')
                            <div>
                                @if($section->title)
                                    <p class="font-semibold leading-6 text-lg dark:text-white">
                                        {{ $section->title }}
                                    </p>
                                @endif
                                <ul role="list" class="mt-6 space-y-4">
                                    @foreach($popularCategories as $category)
                                        <li>
                                            @if($category->parent)
                                                <!-- This is a subcategory -->
                                                <a wire:key="popular-category-{{ $category->id }}" href="{{ generate_category_url($category->adType, $category->parent, $category) }}" class="leading-6 dark:hover:text-white">
                                                    {{ $category->name }}
                                                </a>
                                            @else
                                                <!-- This is a main category -->
                                                <a wire:key="popular-category-{{ $category->id }}" href="{{ generate_category_url($category->adType, $category) }}" class="leading-6 dark:hover:text-white">
                                                    {{ $category->name }}
                                                </a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            @if($section->title)
                                <p class="font-semibold leading-6 text-lg dark:text-white">
                                    {{ $section->title }}
                                </p>
                            @endif
                            <ul role="list" class="mt-6 space-y-4">
                                @foreach($section->footerItems as $item)
                                    <li wire:key='footer-item-{{$item}}'>
                                        @if($item->type === 'page' && $item->page->status!='hidden')
                                        <a href="{{ route('page-details', $item->page->slug) }}" class="leading-6 dark:hover:text-white">
                                            {{ $item->name }}
                                        </a>
                                        @elseif($item->type === 'url')
                                            <a href="{{ $item->url }}" class="leading-6 dark:hover:text-white">
                                                {{ $item->name }}
                                            </a>
                                        @elseif($item->type === 'predefined')
                                            @if($item->predefined_identifier === 'blog' && $blogSettings->enable_blog)
                                                <a href="/blog" class="leading-6 dark:hover:text-white">
                                                    {{ $item->name }}
                                                </a>
                                            @elseif($item->predefined_identifier === 'contact_us')
                                                <a href="/contact" class="leading-6 dark:hover:text-white">
                                                    {{ $item->name }}
                                                </a>
                                            @endif
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-16 border-t dark:border-white/10 border-gray-200 pt-8 classic:border-black">
                <p class="text-center">
                    © {{ now()->year }} {{ $generalSettings->site_name }}. {{ __('messages.t_all_rights_reserved') }}
                </p>
            </div>
        </div>

    </div>
</footer>
