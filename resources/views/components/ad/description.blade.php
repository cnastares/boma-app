@props(['description'])

<div class=" ltr:p-4 rtl:p-8 @if (is_vehicle_rental_active()) border-t border-gray-200 dark:border-white/20 classic:border-black @endif">
    <h2 class="text-xl mb-4 font-semibold">{{ __('messages.t_description') }}:</h2>
    <span class="prose prose-slate dark:prose-invert break-words" style="width: 100% !important">
       {!! $description !!}
    </span>
</div>
