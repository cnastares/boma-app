@props(['facebook_link','twitter_link','linkedin_link','instagram_link'])
<div class="flex items-center gap-x-4">
    @if (!empty($facebook_link))
        <a href="{{ $facebook_link }}" aria-label="facebook" x-data x-tooltip="{
            content: '{{__('messages.t_tooltip_facebook')}}',
            theme: $store.theme,
        }"  class="text-gray-700 hover:text-blue-800  transition duration-300">
            <span class="sr-only">{{__('messages.t_visit_our_facebook_profile')}}</span>
            <x-icon-facebook-2 class="w-7 h-7" />
        </a>
    @endif

    @if(!empty($twitter_link))
        <a href="{{ $twitter_link }}"  aria-label="twitter" class="text-gray-700 dark:stroke-black  hover:text-blue-600 transition duration-300"
        x-data x-tooltip="{
            content: '{{__('messages.t_tooltip_twitter')}}',
            theme: $store.theme,
        }">
        <span class="sr-only">{{__('messages.t_visit_our_twitter_profile')}}</span>
            <div class="group w-[28px] h-[28px]">
                <svg class="w-full h-full fill-white dark:fill-black rounded-md" width="48px" height="48px" viewBox="0 0 30 30"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="30" height="30"
                        rx="4" fill="#3F3F46"></rect>
                    <path
                        d="M19.7447 7.54297H22.2748L16.7473 13.8605L23.25 22.4574H18.1584L14.1705 17.2435L9.60746 22.4574H7.07582L12.9881 15.7L6.75 7.54297H11.9708L15.5755 12.3087L19.7447 7.54297ZM18.8567 20.943H20.2587L11.209 8.97782H9.7046L18.8567 20.943Z">
                    </path>
                </svg>
            </div>
            {{-- <x-icon-twitter-1 class="w-[30px] h-[30px] rounded-lg dark:!stroke-black" /> --}}
        </a>
    @endif

    @if (!empty($linkedin_link))
        <a href="{{ $linkedin_link }}"  aria-label="linkedin"
        x-data x-tooltip="{
            content: '{{__('messages.t_tooltip_linkedin')}}',
            theme: $store.theme,
        }"
            class="text-gray-700 hover:text-blue-900 transition  duration-300">
            <x-icon-linkedin class="w-7 h-7" />
            <span class="sr-only">{{__('messages.t_visit_our_linkedin_profile')}}</span>
        </a>
    @endif

    @if (!empty($instagram_link))
        <a href="{{ $instagram_link }}"  aria-label="instagram"
        x-data x-tooltip="{
            content: '{{__('messages.t_tooltip_instagram')}}',
            theme: $store.theme,
        }"
            class="text-gray-700 hover:text-pink-800 transition duration-300">
            <span class="sr-only">{{__('messages.t_visit_our_instagram_profile')}}</span>
            <x-icon-instagram-2 class="w-7 h-7" />
        </a>
    @endif
</div>
