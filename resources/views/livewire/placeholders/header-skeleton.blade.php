<header
    x-data="{
        isSticky: false,
        sidebarOpen: false,
        @if(isset($this->isMobileHidden))
            isMobileHidden: @entangle('isMobileHidden'),
        @else
            isMobileHidden: true,
        @endif
        isAuthenticated: {{ auth()->user() ? 'true' : 'false' }}
    }"
    @scroll.window="isSticky = (window.pageYOffset > 50)"
    class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 classic:ring-black classic:border-t-2 classic:border-black"
    :class="{ 'sticky top-0 z-30': isSticky, 'hidden md:block': isMobileHidden }"
    aria-live="polite"
>
    <div class="container mx-auto py-4 md:py-6 px-4" aria-busy="true">
        <!-- Loading indicator for screen readers -->
        <span class="sr-only">{{ __('messages.t_aria_label_loading_content') }}</span>

        <!-- Main Header Content -->
        <div class="block md:flex justify-between items-center" aria-busy="true">
            <!-- Logo and Search Area -->
            <div class="flex items-center flex-wrap md:flex-row md:flex-nowrap gap-y-4">
                <!-- Logo Placeholder -->
                <div class="w-32 h-10 bg-gray-200 animate-pulse rounded md:mr-4" aria-hidden="true" role="none">
                    <span class="sr-only">{{ __('messages.t_aria_label_logo_loading') }}</span>
                </div>

                <!-- Search Bar Placeholder -->
                <div class="relative md:mr-4 w-full md:w-auto" aria-hidden="true" role="none">
                    <div class="w-5 h-5 rounded-full animate-pulse bg-gray-200 dark:bg-gray-600 absolute left-0 top-0 mt-[.6rem] md:mt-[.6rem] ml-2" aria-hidden="true" role="none">
                        <span class="sr-only">{{ __('messages.t_aria_label_search_icon_loading') }}</span>
                    </div>
                    <div class="shadow-sm ring-1 ring-gray-950/10 bg-gray-200 animate-pulse dark:bg-gray-600 h-10 md:h-10 pl-10 pr-4 w-full md:min-w-[200px] lg:min-w-[350px] rounded-xl" aria-hidden="true" role="none">
                        <span class="sr-only">{{ __('messages.t_aria_label_search_bar_loading') }}</span>
                    </div>
                </div>
            </div>

            <!-- User Menu Placeholder -->
            <div class="md:flex items-center hidden" aria-hidden="true" role="none">
                <div class="w-10 h-10 bg-gray-200 animate-pulse rounded-full mr-4" aria-hidden="true" role="none">
                    <span class="sr-only">{{ __('messages.t_aria_label_user_avatar_loading') }}</span>
                </div>
                <div class="w-20 h-4 bg-gray-200 animate-pulse rounded mr-4" aria-hidden="true" role="none">
                    <span class="sr-only">{{ __('messages.t_aria_label_user_name_loading') }}</span>
                </div>
                <div class="w-32 h-10 bg-gray-200 animate-pulse rounded" aria-hidden="true" role="none">
                    <span class="sr-only">{{ __('messages.t_aria_label_user_menu_loading') }}</span>
                </div>
            </div>
        </div>

        <!-- Navigation Links Placeholder -->
        <div class="hidden md:block mt-8 mb-4" aria-hidden="true" role="none">
            <div class="flex space-x-4">
                <div class="h-4 bg-gray-200 animate-pulse rounded w-24" aria-hidden="true" role="none">
                    <span class="sr-only">{{ __('messages.t_aria_label_nav_link_loading') }}</span>
                </div>
                <div class="h-4 bg-gray-200 animate-pulse rounded w-24" aria-hidden="true" role="none">
                    <span class="sr-only">{{ __('messages.t_aria_label_nav_link_loading') }}</span>
                </div>
                <div class="h-4 bg-gray-200 animate-pulse rounded w-24" aria-hidden="true" role="none">
                    <span class="sr-only">{{ __('messages.t_aria_label_nav_link_loading') }}</span>
                </div>
                <div class="h-4 bg-gray-200 animate-pulse rounded w-24" aria-hidden="true" role="none">
                    <span class="sr-only">{{ __('messages.t_aria_label_nav_link_loading') }}</span>
                </div>
                <div class="h-4 bg-gray-200 animate-pulse rounded w-24" aria-hidden="true" role="none">
                    <span class="sr-only">{{ __('messages.t_aria_label_nav_link_loading') }}</span>
                </div>
            </div>
        </div>
    </div>
</header>
