<div class=" bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:rounded-xl flex flex-col classic:ring-black h-full pb-5 md:pb-0 mb-5"
    x-data="{
        minPrice: $wire.entangle('minPrice'),
        maxPrice: $wire.entangle('maxPrice'),
    }">

    <div
        class="flex-grow overflow-y-auto hide-scrollbar divide-y divide-gray-200 dark:divide-white/10 classic:divide-black">
        <!-- Category List -->
        @include('livewire.ad-type._parties.filter.load-categories')

        <!-- Sort by -->
        @include('livewire.ad-type._parties.filter.sort-by')

        <!-- Price Range -->
        @include('livewire.ad-type._parties.filter.price-range')

        <!-- Date Range -->
        @include('livewire.ad-type._parties.filter.date-range')

        <!-- Dynamic field -->
        @include('livewire.ad-type._parties.filter.dynamic-field')

        <!-- Apply Button -->
        <div class="p-4">
            <button type="button" class="hidden md:block w-full px-4 border classic:border-black  py-1 rounded-lg cursor-pointer disabled:cursor-not-allowed disabled:text-gray-500 bg-primary-500"
                wire:click='applyFilters'>
                {{ __('messages.t_apply') }}
            </button>
        </div>
    </div>


    @if (!is_vehicle_rental_active())
    <div class="py-2 px-4 md:hidden cusor-pointer">
        <x-button.secondary
            x-on:click="$dispatch('close-filter');document.documentElement.style.overflow = ''; document.body.style.overflow = '';"
            wire:click='applyFilters'
            class="w-full dark:!bg-primary-600">{{ __('messages.t_see_filtered_results') }}</x-button.secondary>
    </div>
    @endif

    <!-- Custom Style -->
    @include('livewire.ad-type._parties.filter.style')

    <!-- Custom Script -->
    @include('livewire.ad-type._parties.filter.script')
</div>
