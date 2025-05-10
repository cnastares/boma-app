@if (is_ecommerce_active() && $ad->adType?->marketplace == ONLINE_SHOP_MARKETPLACE)
<div class="border border-gray-200 dark:border-white/20  classic:border-black border-l-0 border-r-0 border-t-0 rounded-none p-4"
    x-data="{ policy: false, }">
    <div class="flex justify-between items-center gap-x-5">
        @if (isECommerceQuantityOptionEnabled())
        <div class="w-full">
            <div class="flex items-center mt-4 gap-x-2">
                <span class="text-sm md:text-base">{{ __('messages.t_quantity') }}</span>
            </div>
            <x-input wire:model.live='cartQuantity' min="1" type="number"
                max='{{ getECommerceMaximumQuantityPerItem() }}' value="1"
                class="my-3 border border-[#B0B0B0] classic:border-black dark:border-white/10 focus:ring-0 focus:!outline-none dark:focus-within:border-2 dark:focus-within:!border-primary-600 w-[70%] dark:!bg-zinc-800 !rounded" />
        </div>
        @endif
        <div class=" flex items-center gap-x-1 @if (isECommerceQuantityOptionEnabled()) mt-10 @else mb-5 @endif">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M9.47998 1.49897C9.52227 1.3958 9.5943 1.30755 9.6869 1.24543C9.7795 1.18331 9.88848 1.15015 9.99998 1.15015C10.1115 1.15015 10.2205 1.18331 10.3131 1.24543C10.4057 1.30755 10.4777 1.3958 10.52 1.49897L12.645 6.60997C12.6848 6.70561 12.7501 6.78841 12.834 6.84928C12.9178 6.91015 13.0167 6.94672 13.12 6.95497L18.638 7.39697C19.137 7.43697 19.339 8.05997 18.959 8.38497L14.755 11.987C14.6764 12.0542 14.6179 12.1417 14.5858 12.2399C14.5537 12.3382 14.5493 12.4434 14.573 12.544L15.858 17.929C15.8838 18.037 15.877 18.1503 15.8385 18.2545C15.8 18.3587 15.7315 18.4491 15.6416 18.5144C15.5517 18.5797 15.4445 18.6168 15.3335 18.6212C15.2225 18.6256 15.1127 18.597 15.018 18.539L10.293 15.654C10.2048 15.6001 10.1034 15.5715 9.99998 15.5715C9.89659 15.5715 9.79521 15.6001 9.70698 15.654L4.98198 18.54C4.88724 18.598 4.77743 18.6266 4.66644 18.6222C4.55544 18.6178 4.44823 18.5807 4.35835 18.5154C4.26847 18.4501 4.19994 18.3597 4.16143 18.2555C4.12292 18.1513 4.11615 18.038 4.14198 17.93L5.42698 12.544C5.45081 12.4434 5.44643 12.3381 5.41432 12.2399C5.38221 12.1416 5.32362 12.0541 5.24498 11.987L1.04098 8.38497C0.956324 8.3128 0.894988 8.21714 0.864741 8.11009C0.834494 8.00304 0.836696 7.88942 0.87107 7.78362C0.905443 7.67782 0.970441 7.58461 1.05783 7.51578C1.14522 7.44695 1.25107 7.4056 1.36198 7.39697L6.87998 6.95497C6.98323 6.94672 7.0822 6.91015 7.16601 6.84928C7.24981 6.78841 7.3152 6.70561 7.35498 6.60997L9.47998 1.49897Z"
                    fill="#FDAE4B" stroke="black" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class=" flex items-center gap-x-1 pt-1">
                <div class="">
                    {{ number_format($ad->customerReviews()->avg('rating'), 1) }}
                </div>
                <div class=" w-1 h-1 bg-black rounded-full"></div>
                <button type="button" @click="$dispatch('open-modal', {id: 'view-review'});"
                    class="cursor-pointer font-semibold underline underline-offset-1 whitespace-nowrap">
                    {{ $ad->customerReviews()->count() }} Reviews
                </button>
            </div>
        </div>
    </div>
    @if ($ad->returnPolicy)
    <div class=" flex items-center justify-between">
        <div class=" flex items-center gap-x-1">
            <div class=" w-2 h-2 bg-[#90EE90] rounded-full"></div>
            <div class=" text-sm">{{ $ad->returnPolicy?->policy_name }}</div>
        </div>
        <button type="button" @click="policy = true" class=" text-sm underline underline-offset-1 cursor-pointer">Refund
            Policy
        </button>
    </div>
    @endif

    @php
    $cartQuantity = 0;
    if (auth()->check()) {
        $cart = auth()->user()->carts()->where('ad_id', $ad->id)->first();
        if ($cart) {
            $cartQuantity = $cart->quantity;
        }
    } else {
        $sessionCart = session()->get('cart', []);
        foreach ($sessionCart as $item) {
            if ($item['ad_id'] == $ad->id) {
                $cartQuantity = $item['quantity'];
                break;
            }
        }
    }
    @endphp

    <div class=" pt-5">
        @if (isECommerceAddToCardEnabled())
        <x-button wire:click="addToCart()"  size="lg"
            class="w-full mb-4 bg-[#90EE90] font-semibold border-black text-black" disabled="{{auth()->id() == $ad->user_id || isECommerceQuantityOptionEnabled() && $cartQuantity >= getECommerceMaximumQuantityPerItem() }}">
             {{__('messages.t_add_to_cart') }}
        </x-button>
        @endif
        @if (isECommerceBuyNowEnabled())
        <x-button.secondary wire:click="buyNow()" size="lg" class="w-full mb-4" disabled="{{auth()->id() == $ad->user_id }}">{{ __('messages.t_buy_now') }}
        </x-button.secondary>
        @endif
    </div>
    <div x-show="policy"
        class="fixed inset-0 flex items-end lg:items-center justify-center z-50 bg-black dark:bg-opacity-90 bg-opacity-50"
        x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
        <div @click.outside="policy = false"
            class="bg-white rounded-ss-xl rounded-se-xl md:rounded-xl w-[40rem] h-fit dark:bg-gray-800 dark:border-white/10 dark:border p-5">
            <div class="flex justify-end">
                <svg @click="policy = false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class=" w-5 h-5 cursor-pointer">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </div>
            <p class="text-lg font-semibold flex items-center gap-x-1">
                {{ $ad->returnPolicy?->policy_name }}
            </p>
            <p class="pt-5">{{ $ad->returnPolicy?->description }}</p>
        </div>
    </div>
</div>
@endif