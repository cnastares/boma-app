@if ($filterableFieldData && $filterableFieldData->isNotEmpty())
<div class="border-t border-gray-200 dark:border-white/10 px-4 py-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.t_dynamic_filters') }}</h3>

    @foreach ($filterableFieldData as $fieldName => $fieldValues)
    <div class="mt-4">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $fieldName }}</h4>
        <div class="mt-2 space-y-2">
            @foreach ($fieldValues as $index => $fieldValue)
            <div class="flex items-center">
                <input name="field-{{ $fieldName }}-{{ $index }}"
                    type="checkbox"
                    id="field-value-{{ $fieldName }}-{{ $index }}"
                    wire:model.live="filterData.{{ $fieldName }}.{{ $fieldValue['id'] }}"
                    class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-800 dark:border-white/10 dark:text-white dark:focus:ring-primary-500">
                <label for="field-value-{{ $fieldName }}-{{ $index }}" class="ml-3 text-sm text-gray-600 dark:text-white hover:cursor-pointer">
                    {{ is_array($fieldValue['value']) ? implode(', ', $fieldValue['value']) : $fieldValue['value'] }}
                </label>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endif

@if (count($selectableFilters))
<div class=" py-4">
    @foreach ($selectableFilters as $field)
    <div class="py-2 px-4" wire:key='select-field-{{ $field->id }}'>
        <x-label class="mb-2 font-medium" for="min-price" value="{{ $field->name }}" />
        <x-filament::input.wrapper>
            <x-filament::input.select
                wire:model="selectFilterData.{{ $field->name . '_' . $field->id }}">
                <option value="null">Select Filter</option>
                @foreach ($field->options as $index => $value)
                <option value="{{ $index }}"
                    wire:key='select-value-{{ $index . ' -' . $value }}'>{{ $value }}
                </option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>
    @endforeach
</div>
@endif
