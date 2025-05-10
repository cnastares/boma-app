@if ($adType?->filter_settings['enable_date_range'])
<div class="px-2 py-3 overflow-x-auto">
    <!-- start Date -->
    <h3 class="px-2 font-medium">Date</h3>
    <div class="pb-4 pt-2 px-2 gap-2 flex items-center justify-between w-full">
        <div class="w-full">
            <style>
                @media (max-width: 678px) {
                    .date-filter {
                        position: relative;
                        width: 100%;
                    }

                    .date-filter:before {
                        color: lightgray;
                        position: absolute;
                        content: attr(placeholder);
                        padding-left: 10px;
                        left: 0;
                    }
                }
            </style>
            <x-filament::input.wrapper>
                <x-filament::input class=" date-filter dark:!text-gray-500" type="date"
                    wire:model="startDate" placeholder="{{ !$startDate ? 'dd/mm/yyyy' : '' }}" />
            </x-filament::input.wrapper>
        </div>

        <x-heroicon-o-arrow-right class="text-gray-500 h-4 w-5" />
        <!-- end Date -->
        <div class="w-full">
            <x-filament::input.wrapper>
                <x-filament::input type="date" class="date-filter dark:!text-gray-500"
                    wire:model="endDate" placeholder="{{ !$endDate ? 'dd/mm/yyyy' : '' }}" />
            </x-filament::input.wrapper>
        </div>
    </div>
</div>
@endif