<div class="relative cursor-pointer">
    <x-icon-bell-notification class="w-[1.675rem] h-[1.675rem] mt-[0.15rem] dark:text-gray-400 rtl:mr-3 " />
    @if($unreadNotificationsCount > 0)
    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs px-1 py-0.5 min-w-[1.25rem] block rounded-full text-center">
        {{ $unreadNotificationsCount }}
    </span>
    @endif
</div>
