<x-filament-panels::page>
    
    <form wire:submit='save' novalidate>
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit" color="primary">
                Save Changes
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
