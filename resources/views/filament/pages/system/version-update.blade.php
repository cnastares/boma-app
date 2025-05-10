<x-filament-panels::page>




    @if($this->uploadProgress)
    <div>
        <div class="flex justify-between mb-1">
            <span class="text-base font-medium text-green-700 dark:text-white">Uploading...</span>
            <span class="text-sm font-medium text-green-700 dark:text-white">{{ $this->uploadProgress }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $this->uploadProgress }}%"></div>
        </div>
    </div>
    @endif

    @if ($this->buildVersion == $this->latestBuildVersion)
        <div class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 border border-green-800 dark:bg-gray-800 dark:text-green-400" role="alert">
            <x-heroicon-s-check-circle class="flex-shrink-0 w-6 h-6" />
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                Great! You're using the latest version of this app.
            </div>
        </div>
    @else
        <div class="flex items-center p-4 mb-4 text-orange-800 rounded-lg bg-orange-50 border border-orange-800 dark:bg-gray-800 dark:text-orange-400" role="alert">
            <x-heroicon-s-exclamation-circle class="flex-shrink-0 w-6 h-6" />
            <span class="sr-only">Update Available</span>
            <div class="ms-3 text-sm font-medium">
                A new update is available! To upgrade, simply upload the latest version's ZIP file from CodeCanyon and click the "Update To Latest" button. Stay up-to-date with the latest features and improvements.
            </div>
        </div>
    @endif

    @if ($this->buildVersion != $this->latestBuildVersion)
        <div class="flex items-center p-4 mb-8 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800" role="alert">
            <x-heroicon-o-exclamation-circle class="w-6 h-6" />
            <div class="ml-3 text-sm font-medium">
                Before proceeding with the update, please ensure that your PHP memory limit is set to at least 256M and the 'ziparchive' extension is enabled. These settings are crucial for the update process to complete successfully.
            </div>
        </div>
    @endif

    <form wire:submit='versionUpdateExecute' novalidate>
        {{ $this->form }}
        @if ($this->buildVersion != $this->latestBuildVersion)
        <div class="mt-4">
            <x-filament::button type="submit" color="primary" >
                Update to Latest
            </x-filament::button>
        </div>
        @endif
    </form>

    <!-- Manual Update Section -->
    @if ($this->buildVersion != $this->latestBuildVersion)
        <div class="mt-6">
            <h2 class="text-lg font-medium">Manual Update</h2>
            <p class="mt-2">
                If you prefer a manual update, follow these steps:
                <ol class="list-decimal pl-6 mt-2">
                    <li>Download the latest version's ZIP file from CodeCanyon.</li>
                    <li>Rename the downloaded file to <code>source-code.zip</code>.</li>
                    <li>Place it in your application's <code>storage/app/</code> directory. Then click the button below to initiate the update.</li>
                </ol>
            </p>
            <div class="mt-4">
                <x-filament::button wire:click="manualUpdate" color="primary">
                    Manual Update
                </x-filament::button>
            </div>
        </div>
    @endif

</x-filament-panels::page>
