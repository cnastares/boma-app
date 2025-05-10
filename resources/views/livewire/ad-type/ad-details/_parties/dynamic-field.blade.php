@php
$otherGroup = $fieldDetails['Other'] ?? []; // Collect 'Other' group separately if it exists
unset($fieldDetails['Other']); // Remove 'Other' from the main array to process it last
@endphp

{{-- First render fields without a group (if any exist) --}}
@foreach ($otherGroup as $fieldDetail)
@if ($fieldDetail['value'])
<div wire:key="field-{{ $fieldDetail['field_id'] }}">
    <span
        class="font-medium text-lg break-all whitespace-nowrap">{{ $fieldDetail['field_name'] }}:</span>
    @if (is_array($fieldDetail['value']))
    <div class="flex gap-2">
        @foreach ($fieldDetail['value'] as $value)
        <span
            class="inline-block bg-gray-200 hover:bg-gray-300 rounded-full px-3 py-1 text-sm md:text-base font-semibold text-gray-700 mr-2 mb-2 capitalize">{{ $value }}</span>
        @endforeach
    </div>
    @else
    <span
        class="text-sm md:text-base ml-2 break-all">{{ $fieldDetail['value'] }}</span>
    @endif
</div>
@endif
@endforeach

{{-- Then render fields with groups --}}
@foreach ($fieldDetails as $groupName => $fields)
<div
    class="pt-4 pb-1 !mt-5  border-t border-gray-200 dark:border-white/20 classic:border-black">
    <span class="font-semibold text-lg">{{ $groupName }}:</span>
</div>

@foreach ($fields as $fieldDetail)
@if ($fieldDetail['value'])
<div wire:key="field-{{ $fieldDetail['field_id'] }}" class="flex items-center">
    <span class="font-medium text-lg">{{ $fieldDetail['field_name'] }}:</span>
    @if (is_array($fieldDetail['value']))
    <div class="flex gap-2">
        @foreach ($fieldDetail['value'] as $value)
        <span
            class="inline-block bg-gray-200 hover:bg-gray-300 rounded-full px-3 py-1 text-sm md:text-base font-semibold text-gray-700 mr-2 mb-2 capitalize">{{ $value }}</span>
        @endforeach
    </div>
    @else
    <span
        class="text-sm md:text-base ml-2 inline-block">{{ $fieldDetail['value'] }}</span>
    @endif
</div>
@endif
@endforeach
@endforeach