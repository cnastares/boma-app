<div id="alert-additional-content-3"
    class="p-4 mb-4 bg-white rounded-xl border border-gray-200 dark:border-white/10 classic:border-black  dark:bg-gray-800  sm:flex  items-center gap-x-1"
    role="alert">
    <div>
        <img src="{{ asset('/images/offer.svg') }}" alt="Special Offer Icon" class="w-16 h-16 mb-4">
    </div>
    <div class="items-center sm:flex justify-between flex-grow">
        <div class="items-center flex gap-x-2">
            <div>
                <h2 class="text-lg font-semibold">{{ __('messages.t_enhance_sales_title') }}</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('messages.t_enhance_sales_description') }}
                </p>
            </div>
        </div>

        <div class="flex items-center mt-3 sm:mt-0">
            <x-filament::button href="{{ route('filament.app.pages.choose-package') }}" tag="a">
                {{ __('messages.t_check_packages_button') }}
            </x-filament::button>
        </div>
    </div>
</div>
