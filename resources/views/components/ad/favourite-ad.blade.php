@props(['isFavourited'])
<button tabindex="0"   @click.prevent="$wire.addToFavourites" class="cursor-pointer" x-data x-tooltip="{
    content: '{{$isFavourited?__('messages.t_tooltip_favourited'):__('messages.t_tooltip_favourite')}}',
    theme: $store.theme,
}"
aria-pressed="{{ $isFavourited ? 'true' : 'false' }}"
aria-label="{{ $isFavourited ? __('messages.t_aria_label_favourited') : __('messages.t_aria_label_favourite') }}"
>
    @if (optional(current($customizationSettings->ad_detail_page))['enable_favourite_move_to_ad_action'] && Route::currentRouteName() == 'ad-details')
        @if($isFavourited)
            <div class="flex items-center gap-x-1 cursor-pointer transition-all hover:transform hover:-translate-y-1">
                <x-icon-solid-heart-circle class="w-6 h-6 dark:text-gray-400" aria-hidden="true" /> <span>{{ __('messages.t_favorited')}}</span>
            </div>
        @else
            <div class="flex items-center gap-x-1 cursor-pointer transition-all hover:transform hover:-translate-y-1">
                <x-icon-heart class="w-5 h-5 dark:text-gray-400" aria-hidden="true" /> <span>{{ __('messages.t_favorite')}}</span>
            </div>
        @endif

    @else
        @if($isFavourited)
        <x-icon-solid-heart-circle class="md:w-12 md:h-12" aria-hidden="true" />
        @else
        <x-icon-heart class="md:w-11 md:h-11 dark:text-gray-400" aria-hidden="true" />
        @endif
    @endif
</button>
