<a draggable="false" href="{{ generate_category_url($category->adType, $category)?? '#' }}" class="mt-1 block min-w-[150px] max-w-[150px] ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  md:px-5 md:py-5 p-3 bg-white rounded-xl classic:ring-black {{$homeSettings->enable_hover_animation?'classic-hover-shadow':''}}" wire:navigate>
    <div class=" flex flex-col items-center">
        <img draggable="false" src="{{ $category->icon }}" alt="{{__('messages.t_icon_for'). $category->name }}" class="h-10 w-10 md:w-12 md:h-12 rtl:scale-x-[-1]  ring-primary-600"
        x-data x-tooltip="{
            content: '{{$category->name}}',
            theme: $store.theme,
        }">
        <div class="mt-3">
            <h3 class="md:text-md md:font-semibold text-center md:rtl:text-right md:ltr:text-left line-clamp-1 text-xs uppercase md:capitalize">{{ strtoupper($category->name) }}</h3>
        </div>
    </div>
</a>
