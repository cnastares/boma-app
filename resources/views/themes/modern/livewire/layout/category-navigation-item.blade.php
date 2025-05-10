<li class="group">
    <a href="{{ generate_category_url($category->adType, $category, null, $locationSlug) }}" class="block  md:text-sm md:py-1 md:px-3  transition-all md:group-hover:transform  border-1  hover:text-primary-600 w-24 md:w-auto mx-1" wire:navigate>
        <img src="{{ $category->icon }}" alt="{{ $category->name }}" class="h-12 w-12 md:w-20 md:h-20 pb-3 mx-auto  md:hidden">
        <span class="text-xs md:text-sm line-clamp-1 whitespace-normal md:line-clamp-none md:whitespace-nowrap text-center border-primary-600 uppercase md:capitalize group-hover:border-b">{{ $category->name }}</span>
    </a>
    <!-- Subcategories Dropdown -->
    {{-- <div class="md:group-hover:block hidden absolute z-10 mt-0 bg-white shadow-lg py-2 rounded-xl ring-1 ring-gray-950/5  dark:bg-gray-900 dark:ring-white/10 classic:ring-black  classic:group-hover:shadow-custom" >
        @foreach($category->subcategories as $subcategory)
            <a wire:key="subcategory-nav-{{ $subcategory->id }}" href="{{ generate_category_url($category->adType, $category, $subcategory, $locationSlug) }}" class="block px-4 py-3 text-sm underline hover:bg-gray-50  dark:hover:bg-white/5 classic:hover:bg-black classic:hover:text-white cursor-pointer" wire:navigate>{{ $subcategory->name }}</a>
        @endforeach
    </div> --}}
</li>
