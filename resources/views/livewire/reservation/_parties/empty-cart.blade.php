<div class="flex flex-col items-center justify-center p-10 w-full">
    <x-not-found description="{{ __('messages.t_your_cart_is_empty_title') }}" />
    <p class="mt-2 text-gray-600">{{ __('messages.t_your_cart_is_empty_description') }}</p>
    <x-button onclick="window.location='/'" size="lg" class="mt-6 inline-block px-6 py-2 dark:bg-white/10 dark:text-white font-medium border-black text-black">{{ __('messages.t_add_more_item') }}</x-button>
</div>