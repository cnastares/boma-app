<x-filament-panels::page>

    <div class="flex items-center p-4 mb-8 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800" role="alert">
        <x-heroicon-o-exclamation-circle class="w-6 h-6" />
        <div class="ml-3 text-sm font-medium">
            Before proceeding with the add addon, please ensure that your PHP memory limit is set to at least 256M and the 'ziparchive' extension is enabled. These settings are crucial for the addon process to complete successfully.
        </div>
    </div>

    @if($uploadProgress)
    <div>
        <div class="flex justify-between mb-1">
            <span class="text-base font-medium text-green-700 dark:text-white">Uploading...</span>
            <span class="text-sm font-medium text-green-700 dark:text-white">{{ $uploadProgress }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $uploadProgress }}%"></div>
        </div>
    </div>
    @endif

    <form wire:submit='uploadAddon' novalidate>
        {{ $this->form }}
        <div class="mt-4">
            <x-filament::button type="submit" color="primary" >
               Upload Addon
            </x-filament::button>
        </div>
    </form>
    <div>
        {{ $this->table }}
    </div>
    <x-filament-actions::modals />
</x-filament-panels::page>
