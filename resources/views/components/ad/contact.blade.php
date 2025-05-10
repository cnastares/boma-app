<div class="text-center @if ((is_ecommerce_active() || isEnablePointSystem()) && in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE])) py-4 @else p-4 pb-5 @endif">
    <div class="hidden md:block">
        @if (!is_ecommerce_active() && !in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]) && !isEnablePointSystem())
        <h3 class="text-xl mb-4 font-semibold">{{ __('messages.t_contact_seller_action') }}</h3>
        @endif
        <x-button.secondary wire:click="chatWithSeller('{{ __('messages.t_is_item_available_query') }}')" size="lg" class="w-full mb-4">{{ __('messages.t_is_item_available_query') }}</x-button.secondary>
        <x-button.secondary wire:click="chatWithSeller('{{ __('messages.t_meetup_availability_query') }}')" size="lg" class="w-full mb-4">{{ __('messages.t_meetup_availability_query') }}</x-button.secondary>
        <x-button.secondary wire:click="chatWithSeller('{{ __('messages.t_price_negotiation_query') }}')" size="lg" class="w-full mb-4">{{ __('messages.t_price_negotiation_query') }}</x-button.secondary>
    </div>
    <x-button wire:click="chatWithSeller()" size="lg" class=" w-full md:mb-4 bg-[#90EE90] border-black text-black">{{ __('messages.t_chat_with_seller_action') }}</x-button>
</div>
