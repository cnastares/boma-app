<div>
    <x-modal.index id="sidebar" alignment="start" slideOver="true" width="xl">

        <x-slot name="heading">
            <x-brand />
        </x-slot>

        @if(count(fetch_active_languages()) > 1)
        <x-slot name="description">
            <div class="mt-4">
                <livewire:partials.language-switcher />
            </div>
        </x-slot>
    @endif
    <div>
        <div class="space-y-8">
            @foreach($footerSections as $section)
            <div wire:key='footer-section-{{$section}}' class="{{ $loop->first ? 'flex-grow' : 'flex-grow basis-1/2 lg:basis-1/3' }}">
                    @if($section->predefined_identifier === 'site_with_social')
                    <div class="space-y-8">

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
    </div>
    <div class="mt-8 border-t dark:border-white/10 border-gray-200 pt-6 classic:border-black">
        <p class="text-center">
            © {{ now()->year }} {{ $generalSettings->site_name }}. {{ __('All rights reserved.') }}
        </p>
    </div>
    </x-modal.index>
</div>
