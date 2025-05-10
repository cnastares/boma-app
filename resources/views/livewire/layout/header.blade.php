<header x-data="{ isSticky: false, sidebarOpen: false, @if(isset($this->isMobileHidden))
    isMobileHidden: @entangle('isMobileHidden'),
@else
    isMobileHidden: true,
@endif  isAuthenticated: {{ auth()->user() ? 'true' : 'false' }} }"
    @scroll.window="isSticky = (window.pageYOffset > 50)"
    :class="{ 'sticky top-0 z-50': isSticky, 'hidden md:block': isMobileHidden }"
    class="bg-white transition-all dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 classic:ring-black classic:border-t-2 classic:border-black"
    x-show="!isMobileHidden || !isMobile" x-cloak
    @update-sticky-value.window="isSticky=$event.detail?true:false;" @close-modal.window="isSticky=(window.pageYOffset > 50);"
    role="banner"
    >
    <style>
        @media (min-width: 1024px) {
            .search-bar {
                padding-top: @php echo $this->homeSettings->header_top_spacing @endphppx !important;
                padding-bottom: @php echo $homeSettings->header_bottom_spacing @endphppx !important;
            }

            .search-box {
                width: @php echo $this->homeSettings->search_box_size @endphppx !important;
            }
        }

        @media (min-width: 1280px) {
            .search-box {
                width: @php echo $this->homeSettings->lg_search_box_size @endphppx !important;
            }
        }
        .sticky-scroll-margin {
            scroll-margin-top: 160px !important;
        }
    </style>

    <div class="container mx-auto search-bar py-4 md:py-6 px-4" id="main-header">
        <div class=" block md:flex justify-between items-center">
            <div class="flex items-center flex-wrap flex-grow md:flex-row md:flex-nowrap gap-y-4">
                <!-- Logo -->
                <div class="w-auto md:order-1 md:mr-4 flex items-center gap-x-2 ">
                    <button type="button" class="md:hidden" @click="$dispatch('open-modal', { id: 'sidebar' })"
                        x-tooltip="{
                        content: '{{__('messages.t_tooltip_menu_items')}}',
                        theme: $store.theme,
                    }">
                        <x-heroicon-m-bars-3 class="w-6 h-6 text-gray-800 dark:text-white" />
                    </button>
                    <div  x-tooltip="{
                        content: '{{__('messages.t_tooltip_logo')}}',
                        theme: $store.theme,
                    }">
                        <x-brand tabindex="1"  />
                    </div>
                </div>

                <div class="w-auto flex-grow md:flex-grow-0 md:order-3 rtl:mr-2">
                    <livewire:layout.location :$locationSlug />
                </div>
                <div class="w-full md:w-auto md:order-2 flex gap-x-2 justify-between items-center">
                    <div class="flex  grow md:grow-0 lg:min-w-[150px] md:mr-4">
                        <livewire:layout.search-bar :$locationSlug />
                    </div>
                    <div class="md:hidden ml-3 flex items-center">
                        @if($appearanceSettings->enable_theme_switcher)
                        <x-theme-switcher />
                        @endif
                        @if($appearanceSettings->enable_contrast_toggle)
                        <div class='pt-2'>
                            <x-contrast-toggle key="toggle-1" />
                        </div>
                        @endif
                        <x-cart />
                        @if ($isSearch)
                        <div x-on:click="$dispatch('show-filter');">
                            <x-icon-filter class="w-5 h-5 dark:text-gray-400" />
                        </div>
                        @else
                        @if (auth()->check())
                        <div tabindex="0" @keydown.enter="$dispatch('open-modal', { id: 'database-notifications' })" x-tooltip="{
                            content: '{{__('messages.t_tooltip_notifications')}}',
                            theme: $store.theme,
                        }">
                            @livewire('database-notifications')
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="md:flex gap-x-2 items-center hidden">
                @if (count(fetch_active_languages()) > 1)
                <livewire:partials.language-switcher />
                @endif
                @if($appearanceSettings->enable_theme_switcher)
                <x-theme-switcher />
                @endif
                @if($appearanceSettings->enable_contrast_toggle)
                <div class='pt-1'>
                    <x-contrast-toggle key="toggle-2" />
                </div>
                @endif
                <x-cart class="mr-2" />
                <!-- Login -->
                <div x-show="!isAuthenticated" class="rtl:mx-3 ltr:mr-6" x-cloak>
                    <a href="/login">{{ __('messages.t_login') }}</a>
                </div>
                <div x-show="isAuthenticated" class="flex gap-x-4 items-center mr-4 " x-cloak>
                    @php
                    $messagesUrl = app('filament')->hasPlugin('live-chat') && $liveChatSettings->enable_livechat ?
                    '/messages' : '/my-messages';
                    @endphp
                    <div class="hidden lg:block"
                        x-tooltip="{
                        content: '{{__('messages.t_tooltip_messages')}}',
                        theme: $store.theme,
                    }">
                        <a href="{{ $messagesUrl }}">
                            <span class="sr-only">{{__('messages.t_aria_label_messages')}}</span>
                            <x-icon-chat-bubble-text-oval class="w-[1.675rem] h-[1.675rem] dark:text-gray-400" />
                        </a>
                    </div>

                    @if (auth()->check())
                    <div tabindex="0" @keydown.enter.prevent="$dispatch('open-modal', { id: 'database-notifications' })" x-tooltip="{
                        content: '{{__('messages.t_tooltip_notifications')}}',
                        theme: $store.theme,
                    }">
                        @livewire('database-notifications')
                    </div>
                    @endif
                    <x-filament::dropdown placement="top-end">
                        <x-slot name="trigger" x-tooltip="{
                            content: '{{__('messages.t_tooltip_menu_items')}}',
                            theme: $store.theme,
                        }"  >
                            <button type="button" class="flex items-center gap-x-1" aria-label="{{ __('messages.t_aria_label_main_navigation_menu') }}">
                                @if (auth()->check())
                                <div
                                    class="bg-gray-200 dark:bg-black dark:text-gray-100 text-black border-[0.1rem] border-black rounded-full h-8 w-8 flex items-center justify-center">
                                    @if (auth()->user()->profile_image)
                                    <img src="{{ auth()->user()->profile_image }}" alt="{{ auth()->user()->name[0] }}"
                                        class="rounded-full object-cover w-8 h-8">
                                    @else
                                    <span>{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                @endif
                                <x-icon-arrow-down-3 class="w-4 h-4 rtl:ml-1 dark:text-gray-500" aria-hidden="true" />
                            </button>
                        </x-slot>

                        <x-filament::dropdown.list>
                            <x-filament::dropdown.list.item href="\my-profile" tag="a" icon="user-protection-person">
                                {{ __('messages.t_my_profile') }}
                                @include('livewire.user.display-point-balance')
                            </x-filament::dropdown.list.item>

                            @if (auth()->check())
                            <x-filament::dropdown.list.item href="\dashboard" tag="a" icon="heroicon-o-chart-bar-square">
                                {{ __('messages.t_my_dashboard') }}
                            </x-filament::dropdown.list.item>
                            @endif

                            {{-- <x-filament::dropdown.list.item href="\my-ads" tag="a" icon="signage">
                                {{ __('messages.t_my_ads') }}
                            </x-filament::dropdown.list.item> --}}

                            {{-- <x-filament::dropdown.list.item :href="app('filament')->hasPlugin('live-chat') && $liveChatSettings->enable_livechat
                                ? '/messages'
                                : '/my-messages'" tag="a" icon="chat-bubble-text-square">
                                {{ __('messages.t_my_messages') }}
                            </x-filament::dropdown.list.item> --}}


                            <x-filament::dropdown.list.item href="\my-favorites" tag="a" icon="heart-core">
                                {{ __('messages.t_my_favorites') }}
                            </x-filament::dropdown.list.item>

                            {{-- <x-filament::dropdown.list.item href="\verification-center" tag="a"
                                icon="user-protection-person">
                                {{ __('messages.t_verification_center') }}
                            </x-filament::dropdown.list.item> --}}

                            {{-- @if (app('filament')->hasPlugin('packages') && $packageSettings->status)
                            <x-filament::dropdown.list.item href="\my-packages" tag="a" icon="bill">
                                {{ __('messages.t_my_packages') }}
                            </x-filament::dropdown.list.item>
                            @endif --}}

                            {{-- @if (app('filament')->hasPlugin('subscription') && $subscriptionSettings->status && (!$subscriptionSettings->combine_subscriptions_and_orders))
                            <x-filament::dropdown.list.item href="\my-subscriptions" tag="a" icon="bill">
                                {{ __('messages.t_my_subscriptions') }}
                            </x-filament::dropdown.list.item>
                            @endif --}}

                            {{-- <x-filament::dropdown.list.item href="\my-orders" tag="a" icon="bag-dollar">
                                {{ __('messages.t_my_orders') }}
                            </x-filament::dropdown.list.item> --}}

                            @if (app('filament')->hasPlugin('feedback') && $feedbackSettings->enable_feedback)
                            <x-filament::dropdown.list.item href="\my-feedback" tag="a" icon="heroicon-o-chat-bubble-bottom-center-text">
                                {{ __('messages.t_my_feedback') }}
                            </x-filament::dropdown.list.item>
                            @endif

                            {{-- @if (app('filament')->hasPlugin('packages') && $packageSettings->status)
                            <x-filament::dropdown.list.item href="\choose-package" tag="a" icon="list">
                                {{ __('messages.t_buy_business_packages') }}
                            </x-filament::dropdown.list.item>
                            @endif --}}
                            {{-- @if (app('filament')->hasPlugin('subscription') && $subscriptionSettings->status)
                            <x-filament::dropdown.list.item href="\pricing" tag="a" icon="list">
                                {{ __('messages.t_pricing') }}
                            </x-filament::dropdown.list.item>
                            @endif --}}

                            @if (is_ecommerce_active())
                            <x-filament::dropdown.list.item href="/my-purchases" tag="a" icon="heroicon-o-shopping-bag">
                                {{ __('messages.t_my_purchases') }}
                            </x-filament::dropdown.list.item>
                            <x-filament::dropdown.list.item href="/cart-summary" tag="a" icon="heroicon-o-shopping-cart">
                                {{ __('messages.t_cart_summary') }}
                            </x-filament::dropdown.list.item>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-filament::dropdown.list.item icon="logout-1"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('messages.t_logout') }}
                                </x-filament::dropdown.list.item>
                            </form>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>


                </div>
                <!-- Post your ad -->
                <div>
                    <a href="/post-ad"
                        class=" block text-white py-2 px-4 rounded-xl bg-black dark:bg-primary-600 dark:text-white">
                        {{ __('messages.t_post_your_ad') }}
                    </a>
                </div>
            </div>
        </div>
        {{--<div class="hidden md:block">
            <livewire:layout.category-navigation :$context />
        </div>--}}

        <div class="hidden md:block">
            <livewire:layout.ad-type-navigation :$context />
        </div>
    </div>
</header>
