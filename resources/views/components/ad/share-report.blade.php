@props(['ad', 'isFavourited'])
<div class="flex items-center gap-x-6 font-medium gap-y-1">
    <!-- Share Icon with Text -->
    <button type="button" x-on:click="$dispatch('open-modal', { id: 'share-ad' })" class="flex items-center gap-x-1 cursor-pointer transition-all hover:transform hover:-translate-y-1">
        <x-heroicon-o-share class="w-5 h-5" />
        <span>{{ __('messages.t_share') }}</span>
    </button>

    <!-- Report Icon with Text -->
    <button type="button" wire:click="openReportAd" class="flex items-center gap-x-1 cursor-pointer transition-all hover:transform hover:-translate-y-1">
        <x-heroicon-o-shield-exclamation class="w-5 h-5" />
        <span>{{ __('messages.t_report') }}</span>
    </button>

    @if (optional(current($customizationSettings->ad_detail_page))['enable_favourite_move_to_ad_action'])
    <x-ad.favourite-ad :$isFavourited />
    @endif
</div>
