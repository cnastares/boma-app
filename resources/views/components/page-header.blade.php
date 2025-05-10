@props(['title', 'isMobileHidden' => true, 'referrer'])

<header x-data="{ isSticky: false, isMobileHidden: {{ $isMobileHidden }}, referrer: @entangle('referrer'), prevUrl: document.referrer,  prevUrl: document.referrer, currentUrl: window.location.href }" class="flex md:hidden items-center p-4 bg-white border-b classic:border-black dark:bg-gray-900 dark:border-white/10" @scroll.window="isSticky = (window.pageYOffset > 50)"
    :class="{ 'sticky top-0 left-0 right-0 z-40 ': isSticky }"
>
    <a :href="referrer ? referrer : (prevUrl !== currentUrl ? prevUrl : '/')" class="mr-4 cursor-pointer" wire:navigate>
        <!-- Replace with actual back icon -->
        <x-icon-arrow-left-1 class="w-6 h-6 cursor-pointer rtl:scale-x-[-1]" />
        <span class="sr-only">{{__('messages.t_aria_label_back')}}</span>
    </a>
    <p class="text-lg md:text-xl font-medium break-all">{!! $title !!}</p>
</header>
