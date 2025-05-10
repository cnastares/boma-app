<div role="navigation" x-data="{
    activeRoute: window.location.pathname,
    init() { console.log(this.activeRoute); }
}" class="border-b border-gray-950/5 dark:border-white/10 hidden md:block classic:border-black">
    <div class="container mx-auto px-4">
        <div class="flex">
            {{-- <a href="/my-ads"
               :class="activeRoute === '/my-ads' ? 'bg-black text-white dark:bg-primary-600' : ''"
               class="py-3 px-6 border-l border-r border-gray-950/5 dark:border-white/10 classic:border-black" wire:navigate>
               {{ __('messages.t_my_ads') }}
            </a> --}}

            {{-- @if(!app('filament')->hasPlugin('live-chat') && !$liveChatSettings->enable_livechat)
                <a href="/my-messages"
                :class="activeRoute === '/my-messages' ? 'bg-black text-white dark:bg-primary-600' : ''"
                class="py-3 px-6 border-r border-gray-950/5 dark:border-white/10 classic:border-black" wire:navigate>
                {{ __('messages.t_my_messages') }}
            </a>
            @endif --}}

            <a href="/my-favorites" :class="activeRoute === '/my-favorites' ? 'bg-black text-white dark:bg-primary-600' : ''"
                class="py-3 px-6 border-l border-gray-950/5 dark:border-white/10 classic:border-black" >
                {{ __('messages.t_my_favourites') }}
            </a>

            <a href="/my-profile"
                :class="activeRoute === '/my-profile' ? 'bg-black  text-white dark:bg-primary-600' : ''"
                class="py-3 px-6 border-l border-gray-950/5 dark:border-white/10 classic:border-black" >
                {{ __('messages.t_my_profile') }}
            </a>
            {{-- <a href="/verification-center"
                :class="activeRoute === '/verification-center' ? 'bg-black  text-white dark:bg-primary-600' : ''"
                class="py-3 px-6  border-gray-950/5 dark:border-white/10 classic:border-black  border-r" >
                {{ __('messages.t_verification_center') }}
            </a>
            @if(app('filament')->hasPlugin('packages') && $packageSettings->status)
            <a href="/my-packages"
                :class="activeRoute === '/my-packages' ? 'bg-black  text-white dark:bg-primary-600' : ''"
                class="py-3 px-6 border-gray-950/5  border-r  dark:border-white/10 classic:border-black">
                {{ __('messages.t_my_packages') }}
            </a>
            @endif

            @if (app('filament')->hasPlugin('subscription') && $subscriptionSettings->status && (!$subscriptionSettings->combine_subscriptions_and_orders))
            <a href="/my-subscriptions"
                :class="activeRoute === '/my-subscriptions' ? 'bg-black  text-white dark:bg-primary-600' : ''"
                class="py-3 px-6 border-gray-950/5  border-r  dark:border-white/10 classic:border-black">
                {{ __('messages.t_my_subscriptions') }}
            </a>
            @endif --}}

            @if(app('filament')->hasPlugin('feedback') && $feedbackSettings->enable_feedback)
            <a href="/my-feedback"
                :class="activeRoute === '/my-feedback' ? 'bg-black  text-white dark:bg-primary-600' : ''"
                class="py-3 px-6 border-gray-950/5 border-l  dark:border-white/10 classic:border-black">
                {{ __('messages.t_my_feedback') }}
            </a>
            @endif

            @if((is_ecommerce_active() && isECommerceAddToCardEnabled())|| isEnablePointSystem() )
            <a href="/cart-summary"
                :class="activeRoute.startsWith('/cart-summary') ? 'bg-black text-white dark:bg-primary-600' : ''"
                class="py-3 px-6 border-l border-gray-950/5 dark:border-white/10 classic:border-black" >
                {{ __('messages.t_cart_summary') }}
            </a>
            @endif
            @if(is_ecommerce_active() || isEnablePointSystem())
            <a href="{{ route('reservation.my-purchases') }}"
                :class="activeRoute === '/my-purchases' ? 'bg-black text-white dark:bg-primary-600' : ''"
                class="py-3 px-6 border-l border-r border-gray-950/5 dark:border-white/10 classic:border-black" >
                {{ __('messages.t_my_purchases') }}
            </a>
            @endif

            @if(is_vehicle_rental_active())
            <a href="/my-booking"
                :class="activeRoute === '/my-booking' ? ' bg-black  text-white dark:bg-primary-600' : ''"
                class="py-3 px-6 border-l border-gray-950/5  dark:border-white/10 classic:border-black">
                {{ __('messages.t_my_booking') }}
            </a>
            @endif
        </div>
    </div>
</div>
