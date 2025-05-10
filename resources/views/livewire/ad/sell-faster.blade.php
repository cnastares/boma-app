<div id="alert-additional-content-3"
    class="p-4 mb-4 bg-white rounded-xl border border-gray-200 dark:border-white/10 classic:border-black  dark:bg-gray-800  sm:flex  items-center gap-x-2"
    role="alert">
    <div>
        <img src="{{ asset('/images/offer.svg') }}" alt="Special Offer Icon" class="w-16 h-16 mb-4">
    </div>
    <div class="items-center flex justify-between flex-grow">
        <div class="items-center flex gap-x-2 mb-2 flex-grow">
            <div class=" {{ $isHorizontal ? "sm:flex flex-grow justify-between items-center" : "" }}">
                <div>
                    <h2 class="text-lg font-semibold">{{ __('messages.t_increase_sales_title') }}</h2>
                    <p class="mt-2 {{ $isHorizontal ?  "sm:mb-0 mb-4" : "mb-4" }} text-sm text-gray-600 dark:text-gray-300">
                        {{ __('messages.t_increase_sales_description') }}
                    </p>
                </div>
                <div>
                    <x-filament::button wire:click="sellFastNow">
                        {{ __('messages.t_enhance_sales_action_button') }}
                    </x-filament::button>
                </div>
            </div>
        </div>

    </div>
</div>
