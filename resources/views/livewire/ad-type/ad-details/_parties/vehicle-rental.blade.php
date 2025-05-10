@if (is_vehicle_rental_active() && $vehicle?->transmission)
<div class="py-6 px-4">
    <h3 class="text-lg mb-4 font-semibold">{{ __('messages.t_ad_details') }}:</h3>
    <div class="space-y-3">
        <div class=" space-x-2">
            <span
                class="font-medium whitespace-nowrap">{{ __('messages.t_transmission') }}:</span>
            <span
                class="text-gray-600 dark:text-gray-300 grid-cols-1 md:col-span-3">{{ $vehicle->transmission->name }}</span>
        </div>
        <div class=" space-x-2">
            <span
                class="font-medium whitespace-nowrap">{{ __('messages.t_fuel_type') }}:</span>
            <span
                class="text-gray-600 dark:text-gray-300 grid-cols-1 md:col-span-3">{{ $vehicle->fuelType->name }}</span>
        </div>
        <div class=" space-x-2">
            <span class="font-medium whitespace-nowrap">{{ __('messages.t_mileage') }}:</span>
            <span
                class="text-gray-600 dark:text-gray-300 grid-cols-1 md:col-span-3">{{ $vehicle->mileage . ' ' . __('messages.t_mileage_prefix') }}</span>
        </div>
        <div class=" flex items-start gap-x-2">
            <span
                class="font-medium whitespace-nowrap">{{ __('messages.t_vehicle_features') }}:</span>
            <div class="text-gray-600 dark:text-gray-300">
                @foreach ($vehicle->features as $features)
                {{ $features->name }}
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif