<div class="bg-white dark:bg-gray-900 py-1 fixed inset-x-0 bottom-0  shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 md:hidden classic:ring-black z-10"
    x-data="{ activeRoute: window.location.pathname }" id="bottom-navigation">
    <nav class="flex justify-between" aria-label="{{__('messages.t_aria_label_bottom_navigation') }}">
        <a href="/" class="flex flex-col items-center w-1/4 py-2 text-center "
            :class="{ 'font-bold text-primary-600 classic:text-primary-600 dark:text-primary-600': activeRoute === '/' }">
            <x-icon-home x-show="activeRoute !== '/'" class="w-5 h-5" />
            <x-icon-home-s x-show="activeRoute === '/'" class="w-5 h-5 dark:text-primary-600" x-cloak />
            <span>{{ __('messages.t_home_mobile') }}</span>
        </a>

        @php
            $messagesUrl =
                app('filament')->hasPlugin('live-chat') && $liveChatSettings->enable_livechat
                    ? '/messages'
                    : '/my-messages';
        @endphp

        <a href="{{ $messagesUrl }}" class="flex flex-col items-center w-1/4 py-2 text-center"
            :class="{ 'font-bold text-primary-600 classic:text-primary-600 dark:text-primary-600': activeRoute === '{{ $messagesUrl }}' }">
            <x-icon-chat-bubble-text-square x-show="activeRoute !== '{{ $messagesUrl }}'" class="w-5 h-5" />
            <svg x-show="activeRoute === '{{ $messagesUrl }}'" class="w-5 h-5 fill-primary-600" x-cloak
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" height="24" width="24">
                <g id="chat-bubble-text-square--messages-message-bubble-text-square-chat">
                    <path id="Subtract" fill="currentColor" fill-rule="evenodd"
                        d="M22 19.5 22 1H2v21.804L7.287 19.5H22ZM17 9H7V7h10v2ZM7 13.5h7v-2H7v2Z" clip-rule="evenodd">
                    </path>
                </g>
            </svg>
            {{-- <x-icon-chat-bubble-text-square-s x-show="activeRoute === '{{ $messagesUrl }}'" class="w-5 h-5 dark:text-primary-600" x-cloak /> --}}
            <span>{{ __('messages.t_message_mobile') }}</span>
        </a>


        <a href="/dashboard/my-ads" class="flex flex-col items-center w-1/4 py-2 text-center"
            :class="{ 'font-bold text-primary-600 classic:text-primary-600 dark:text-primary-600': activeRoute === '/dashboard/my-ads' }">
            <x-icon-signage x-show="activeRoute !== '/dashboard/my-ads'" class="w-5 h-5" />
            <x-icon-signage-s x-show="activeRoute === '/dashboard/my-ads'" class="w-5 h-5 dark:text-primary-600"
                x-cloak />
            <span>{{ __('messages.t_my_ads_mobile') }}</span>

        </a>

        <a href="/my-account" class="flex flex-col items-center w-1/4 py-2 text-center"
            :class="{ 'font-bold text-primary-600 classic:text-primary-600 dark:text-primary-600': activeRoute === '/my-account' }">
            <x-icon-user-protection-person x-show="activeRoute !== '/my-account'" class="w-5 h-5" />
            <x-icon-user-protection-person-s x-show="activeRoute === '/my-account'"
                class="w-5 h-5 dark:text-primary-600" x-cloak />
            <span>{{ __('messages.t_my_profile_mobile') }}</span>

        </a>
    </nav>
</div>
