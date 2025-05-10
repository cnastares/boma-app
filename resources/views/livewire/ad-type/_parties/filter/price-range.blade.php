@if ($adType?->filter_settings['enable_price_range'])
<div class="{{ $adType?->filter_settings['enable_price_range_toggle'] ? 'range p-4' : 'p-4 gap-2 flex justify-between items-center'}}">

    @if ($adType?->filter_settings['enable_price_range_toggle'])
    <div class="w-[98%] my-3">
        <div class="range-slider">
            <span class="range-selected bg-[black] dark:bg-primary-600"></span>
        </div>
        <div class="range-input pb-2">
            <input type="range" role="slider" class="min" wire:model="minPrice" min="{{ $priceRangeMin }}"
                max="{{ $priceRangeMax }}" value="{{ $minPrice }}" step="10" wire:ignore name="minPrice" aria-label="{{__('messages.t_aria_label_min_price') }}" />
            <input type="range" role="slider" class="max" wire:model="maxPrice" min="{{ $priceRangeMin }}"
                max="{{ $priceRangeMax }}" value="{{ $maxPrice }}" step="10" wire:ignore name="maxPrice" aria-label="{{__('messages.t_aria_label_max_price') }}" />
        </div>
    </div>
    @endif

    <div class="range-price">
        <!-- Min Price class="w-full"-->
        <div class="w-full">
            <x-label class="mb-2 font-medium" for="min-price" value="{{ __('messages.t_min_price') }}" />
            <x-filament::input.wrapper>
                <x-filament::input
                    :placeholder="__('messages.t_min_price_placeholder')"
                    type="text"
                    x-model.live.debounce.500="minPrice"
                    wire:model="minPrice"
                    id="min-price"
                    x-mask="9999999999999999"
                    :disabled="$adType?->filter_settings['enable_price_range_toggle']"
                    />
            </x-filament::input.wrapper>
        </div>

        <!-- Max Price -->
        <div class="w-full">
            <x-label class="mb-2 font-medium" for="max-price" value="{{ __('messages.t_max_price') }}" />
            <x-filament::input.wrapper>
                <x-filament::input
                    :placeholder="__('messages.t_max_price_placeholder')"
                    type="text"
                    x-model.live.debounce.500="maxPrice"
                    wire:model="maxPrice"
                    id="max-price"
                    x-mask="9999999999999999"
                    :disabled="$adType?->filter_settings['enable_price_range_toggle']" />
            </x-filament::input.wrapper>
        </div>
    </div>
</div>
@endif
