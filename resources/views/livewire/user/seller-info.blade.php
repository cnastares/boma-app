<div class="border border-gray-200 dark:border-white/20 rounded classic:border-black {{ $extraClass }}">
    <!-- Seller Profile -->
    <div class="px-6 py-6 @if((!is_ecommerce_active() &&  $ad->adType?->marketplace != ONLINE_SHOP_MARKETPLACE) && (!isEnablePointSystem() && $ad->adType?->marketplace != POINT_SYSTEM_MARKETPLACE)) pb-2 @endif text-sm md:text-base">
        <div class="flex items-center justify-between gap-x-2">
            <div class="text-lg font-semibold flex items-center gap-x-1">
                <x-user.list-item :user="$user" />
                @if($user->verified)
                <x-filament::icon-button
                    icon="heroicon-s-check-badge"
                    tooltip="{{ __('messages.t_user_verified_tooltip') }}"
                    size="lg"
                    color="success" />
                @endif
            </div>

            @if(!is_ecommerce_active() && !in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]) && !isEnablePointSystem())
            <button type="button" wire:click="toggleFollow" class="{{ $this->isFollowing() ? 'bg-gray-900' : '' }} px-3 py-1 rounded-full border classic:border-black cursor-pointer flex items-center"
                x-data x-tooltip="{
                    content: '{{ $this->isFollowing() ?__('messages.t_tooltip_unfollow') : __('messages.t_tooltip_follow') }}',
                    theme: $store.theme,
                }"
                aria-label="{{ $this->isFollowing() ? __('messages.t_tooltip_unfollow') : __('messages.t_tooltip_follow') }}"
                >
                @if($this->isFollowing())
                <x-heroicon-s-user class="w-6 h-6 text-white" aria-hidden="true" />
                <x-heroicon-s-check class="w-5 h-5 -ml-1 text-white" aria-hidden="true" />
                @else
                <x-heroicon-o-user class="w-6 h-6" aria-hidden="true" />
                <x-heroicon-o-plus-small class="w-5 h-5 -ml-1" aria-hidden="true" />
                @endif
            </button>
            @endif
        </div>

        @if($ad && (!is_ecommerce_active() &&  !in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE])) && !isEnablePointSystem())
        <div class="flex items-center mt-4 gap-x-2">
            @if($ad->for_sale_by == 'owner')
            <x-heroicon-o-user class="w-6 h-6" />
            <div>
                <span class="text-sm md:text-base capitalize">{{ __('messages.t_owner') }}</span>
            </div>
            @else
            <x-heroicon-o-briefcase class="w-6 h-6" />
            <div>
                <span class="text-sm md:text-base capitalize">{{ __('messages.t_business') }}</span>
            </div>
            @endif
        </div>
        @endif
        @if ((!is_ecommerce_active() &&  !in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE])) && !isEnablePointSystem())
        <div class="flex items-center mt-4 gap-x-2">
            <x-heroicon-o-users class="w-6 h-6" />
            <div class="flex gap-x-2">
                <div class="cursor-pointer" wire:click="showFollowersModal">
                    <span class="font-semibold">{{ $this->followersCount }}</span> {{ __('messages.t_followers') }}
                </div>
                <div class="text-muted"> | </div>
                <div class="cursor-pointer" wire:click="showFollowingModal">
                    <span class="font-semibold">{{ $this->followingCount }}</span> {{ __('messages.t_following') }}
                </div>
            </div>
        </div>


        <div class="flex items-center mt-4 gap-x-2">
            <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
            <span class="text-sm md:text-base">{{ pluralize($user->ads->count(),__('messages.t_created_ad'),__('messages.t_created_ads'),true) }}</span>
        </div>
        @endif
        <!-- Member Since -->
        <div class="flex items-center mt-4 gap-x-2">
            <x-heroicon-o-calendar-days class="w-6 h-6" />
            <span class="text-sm md:text-base">{{ __('messages.t_member_since') }} {{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('F Y') }}</span>
        </div>

        <!-- Email Verified -->
        @if($user->email_verified_at)
        <div class="flex items-center mt-4 gap-x-2">
            <x-heroicon-o-envelope class="w-6 h-6" />
            <span class="text-sm md:text-base">{{ __('messages.t_email_verified') }}</span>
        </div>
        @endif

        @if($ad && $ad->website_url && $isWebsite && isWebsiteUrlEnabled())
        <x-ad.website websiteURL="{{ $ad->website_url }}" websiteLabel="{{ $ad->website_label }}" />
        @endif

        @if(app('filament')->hasPlugin('feedback') && $feedbackSettings->enable_feedback)
        <x-ad.feedback :user="$user" />
        @endif

        @php
        $canRevealPhone = $phoneSettings->enable_phone && ($phoneSettings->enable_login_user_number_reveal ? auth()->check() : true);
        $phoneNumber = is_vehicle_rental_active() ? $ad->user->phone_number : $ad->phone_number && $ad->display_phone;
        $whatsappNumber = is_vehicle_rental_active() ? $ad->user->whatsapp_number : $ad->whatsapp_number && $ad->display_whatsapp;
        @endphp

        @if ($canRevealPhone && $phoneNumber)
        <x-ad.phone :phoneNumber="is_vehicle_rental_active() ? $ad->user->phone_number : $ad->phone_number" />
        @endif

        @if ($phoneSettings->enable_whatsapp && $whatsappNumber && $revealed)
        <x-button wire:click="chatWithWhatsapp()" size="lg"
            class="w-full bg-gray-50 py-1 px-2 !flex !justify-start mt-4 border-gray-200 classic:border-black text-black dark:bg-[#90EE90]">
            <span class="!flex !justify-start p-1 gap-x-2">
                <img src="{{ asset('/images/logos_whatsapp-icon.svg') }}" class="h-6 w-6">
                <span class="font-medium">{{ __('messages.t_whatsapp_chat') }}</span>
            </span>
        </x-button>
        @endif

        @if ((is_ecommerce_active()|| isEnablePointSystem()) && in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
        <div class=" hidden md:block">
            <x-ad.contact :$ad/>
        </div>
        @endif
    </div>

    @include('livewire.user.modals.followers-modal')
</div>
