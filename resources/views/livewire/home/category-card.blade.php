
<a href="{{generate_category_url($category->adType, $category) }}" class="block min-w-[120px] ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  md:px-5 md:py-5 p-3 bg-white rounded-xl classic:ring-black {{$homeSettings->enable_hover_animation?'classic-hover-shadow':''}}" >
    <div class="flex md:gap-x-3 flex-col md:flex-row items-center md:items-start">
        <img src="{{ $category->icon }}" alt="{{__('messages.t_icon_for'). $category->name }}" class="h-14 w-14 md:w-20 md:h-20 pb-3 rtl:scale-x-[-1]"
        x-data x-tooltip="{
            content: '{{$category->name}}',
            theme: $store.theme,
        }">
        <div>
            <h3 class="md:text-lg md:font-bold text-center md:rtl:text-right md:ltr:text-left line-clamp-1 text-xs uppercase md:capitalize">{{ $category->name }}</h3>
            <p class="py-2 hidden md:block">{{ $category->description }}</p>
            <div class="hidden lg:flex gap-x-7 mt-2">
                    <!-- Sellers Count Section -->
                <div class=" flex gap-x-1.5 items-center text-muted dark:text-gray-400">
                    <x-heroicon-o-user-group class="w-5 h-5 stroke-2 dark:text-gray-500" aria-hidden="true" />
                    <span class=" text-sm ">{{ __('messages.t_sellers_count', ['count' => \Number::format(floatval($sellersCount), locale: $paymentSettings->currency_locale)]) }}</span>
                </div>
                    <!-- Listings Count Section -->
                <div class=" flex gap-x-1.5 items-center text-muted dark:text-gray-400">
                    <x-heroicon-o-queue-list class="w-4 h-4 stroke-2 dark:text-gray-500" aria-hidden="true" />
                    <span class=" text-sm">{{ __('messages.t_listings_count', ['count' => \Number::format(floatval($listingsCount), locale: $paymentSettings->currency_locale)])  }}</span>
                </div>
                    <!-- Sales Count Section -->
                <div class=" flex gap-x-1.5 items-center text-muted dark:text-gray-400">
                    <x-heroicon-o-currency-dollar class="w-4 h-4 stroke-2 dark:text-gray-500" aria-hidden="true" />
                    <span class="text-sm">{{ __('messages.t_sales_count', ['count' => \Number::format(floatval($salesCount), locale: $paymentSettings->currency_locale)])}}</span>
                </div>
            </div>
        </div>
    </div>
</a>

