@props(['websiteURL', 'websiteLabel'])

<div class="flex items-center bg-gray-50 mt-4 cursor-pointer justify-between rounded-xl w-full py-1 px-2 border border-gray-200 dark:border-white/20 classic:border-black" >
    <div class="p-2 flex gap-x-2 items-center">
         <x-heroicon-o-globe-alt  class="w-5 h-5" />
        <div class="font-medium">
            <a target="_blank" href="{{ $websiteURL }}" wire:click="$dispatch('websiteURLClicked')">{{ $websiteLabel ?: $websiteURL }}</a>
        </div>
    </div>
</div>
