@if ($adType?->filter_settings['enable_sort_by'] && $adListSettings->sort_by_position=='filter_box')
<div class="py-6 px-4">
    <x-label for="sort-by" class="mb-2 font-medium" value="{{ __('messages.t_sort_by') }}" />
    <x-filament::input.wrapper>
        <x-filament::input.select wire:model.live="sortBy" id="sort-by">
            <option value="date">{{ __('messages.t_date') }}</option>
            <option value="price_asc">{{ __('messages.t_price_low_to_high') }}</option>
            <option value="price_desc">{{ __('messages.t_price_high_to_low') }}</option>
            <option value="date_asc">{{ __('messages.t_latest') }}</option>
        </x-filament::input.select>
    </x-filament::input.wrapper>
</div>
@endif
