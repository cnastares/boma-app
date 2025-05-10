<a href="#" class="animate-pulse shadow-sm flex-none bg-gray-200 dark:bg-gray-700 rounded-xl hover:shadow-md transition-all hover:transform hover:-translate-x-1 hover:-translate-y-1 relative flex flex-col">
    <!-- Image & Heart Placeholder -->
    <div class="p-2 md:pb-0 relative">
        <div class="absolute top-4 right-4">
            <!-- Heart Icon Placeholder -->
            <div class="w-6 h-6 bg-gray-300 dark:bg-gray-600 rounded-full">
                <span class="sr-only">{{ __('messages.t_aria_label_favorite_loading') }}</span>
            </div>
        </div>
        <!-- Image Placeholder -->
        <div class="bg-gray-300 dark:bg-gray-600 w-full md:h-[12rem] h-32 rounded-xl">
            <span class="sr-only">{{ __('messages.t_aria_label_ad_image_loading') }}</span>
        </div>
    </div>

    <!-- Text Placeholders -->
    <div class="flex-grow flex flex-col">
        <div class="flex-grow md:border-b border-gray-300 dark:border-gray-600 classic:border-gray-300">
            <div class="px-2 md:px-3 py-3 h-full flex flex-col">
                <!-- Ad Title Placeholder -->
                <div class="mb-1 flex-grow bg-gray-300 dark:bg-gray-600 rounded w-3/4 h-4">
                    <span class="sr-only">{{ __('messages.t_aria_label_ad_title_loading') }}</span>
                </div>

                <div class="hidden md:block mb-1 mt-2 bg-gray-300 dark:bg-gray-600 rounded w-1/2 h-4">
                    <span class="sr-only">{{ __('messages.t_aria_label_ad_description_loading') }}</span>
                </div>

                <!-- Location Placeholder -->
                <div class="flex items-center mt-3">
                    <div class="w-5 h-5 bg-gray-300 dark:bg-gray-600 rounded mr-2"></div>
                    <div class="font-light ml-1 bg-gray-300 dark:bg-gray-600 rounded w-20 h-4"></div>
                    <span class="sr-only">{{ __('messages.t_aria_label_ad_location_loading') }}</span>
                </div>
            </div>
        </div>
        <!-- Price & Date Placeholders -->
        <div class="flex justify-between items-center px-2 md:px-3 py-2">
            <div class="bg-gray-300 dark:bg-gray-600 rounded w-16 h-4">
                <span class="sr-only">{{ __('messages.t_aria_label_ad_price_loading') }}</span>
            </div>

            <div class="text-gray-500 dark:text-gray-400 text-sm bg-gray-300 dark:bg-gray-600 rounded w-16 h-4">
                <span class="sr-only">{{ __('messages.t_aria_label_ad_date_loading') }}</span>
            </div>
        </div>
    </div>
</a>
