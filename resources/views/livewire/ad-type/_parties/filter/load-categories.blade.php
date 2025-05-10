@if ($adType?->filter_settings['enable_categories'])
<div x-data="{ queryString: window.location.search, startWatching() { this.interval = setInterval(() => { if (this.queryString !== window.location.search) { this.queryString = window.location.search } }, 100); }, interval: null }" x-init="startWatching()" class="py-6 px-4">
    <div class=" flex justify-between">
        <h2 class="mb-2 font-medium">{{ __('messages.t_categories') }}</h2>
    </div>
    <ul class="overflow-y-auto max-h-96">
        @if ($isMainCategory)
        @foreach ($mainCategories as $category)
        <li wire:key='filter-main-{{ $category->slug }}' class="mb-1 cursor-pointer">
            <a class="focus:-outline-offset-1 focus:z-10" href="{{ generate_category_url($adType, $category) }}"
                wire:navigate>
                {{ $category->name }}
            </a>
        </li>
        @endforeach
        @else
        <!-- Display selected category's parent at the top with a back arrow -->
        <li class="font-medium mb-1 cursor-pointer flex items-center">
            <x-heroicon-o-arrow-left role="button" wire:click="backToMainCategory()" aria-label="{{ __('messages.t_aria_label_back') }}"
                class="w-5 h-5 mr-1 rtl:scale-x-[-1] " />
            <a class="focus:-outline-offset-1 focus:z-10" href="{{ generate_category_url($adType, null) }}"
                wire:navigate>
                {{ $selectedCategory->parent ? $selectedCategory->parent->name : $selectedCategory->name }}
            </a>
        </li>

        @foreach ($subCategories as $subCategory)
        <li wire:key='filter-sub-{{ $subCategory->slug }}'
            class="{{ $subCategory->slug == $subCategorySlug ? 'underline' : '' }} mb-1 pl-10 cursor-pointer">
            <a href="{{ generate_category_url($adType, ($selectedCategory->parent ? $selectedCategory->parent : $selectedCategory), $subCategory) }}"
                wire:navigate>
                {{ $subCategory->name }}
            </a>
        </li>
        @endforeach
        @endif
    </ul>
</div>
@endif
