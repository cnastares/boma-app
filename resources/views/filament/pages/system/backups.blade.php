<x-filament-panels::page>
    <div class="flex flex-col gap-y-8">
        <div class="mb-10">
            <livewire:admin.system.backup-destination-status-list />
        </div>
        <div>
           <livewire:admin.system.backup-destination-list />
        </div>
        <x-filament::modal id="backup-option" width="lg">
            <x-slot name="heading">
                <h3 class="text-xl">Please choose an option</h3>
            </x-slot>

            <x-slot name="footer" >
                <div class="flex gap-x-2">
                    <x-filament::button wire:click="create('only-db')" color="primary" class="w-full">
                        Only DB
                    </x-filament::button>

                    <x-filament::button wire:click="create('only-files')" color="info" class="w-full">
                        Only Files
                    </x-filament::button>

                    <x-filament::button wire:click="create()" color="success" class="w-full">
                        DB & Files
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
    </div>
</x-filament-panels::page>
