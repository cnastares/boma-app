<header x-data="{ isSticky: false, isWebView: @entangle('isWebView') }"
    class=" bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 classic:ring-black"
    :class="{ 'sticky top-0 z-10 ': isSticky, 'invisible h-0': isWebView }"
    @scroll.window="isSticky = (window.pageYOffset > 30)">
    <div class="lg:w-2/3 md:w-3/4 mx-auto px-5">
        <div class=" flex justify-between items-center py-4">
            <div class="flex items-center gap-x-2">
                <div role="button" aria-label="{{ __('messages.t_aria_label_back') }}" id="post-ad-back" wire:click="$parent.back()">
                    <x-icon-arrow-left-1 class="w-6 h-6 cursor-pointer rtl:scale-x-[-1]" />
                </div>
                <h2 class="text-xl font-semibold">{{ $title }}</h2>
            </div>
            @if ($current !== 'ad.post-ad.payment-ad')
                <div
                    class=" flex gap-x-4 fixed left-0 right-0 bottom-0  px-4 pt-3 pb-5 md:p-0 md:static md:bg-transparent dark:bg-gray-900 md:border-none bg-white border-t border-gray-950/5 dark:border-white/10 classic:border-black z-10">
                    <a class="flex items-center" href="{{ route('home') }}" wire:navigate>
                        {{ __('messages.t_cancel') }}
                    </a>

                    @if ($isLastStep)
                        @if (count($selectedPromotions) > 0)
                            <x-button.primary class="min-w-[6rem] w-full md:w-auto" wire:click="payAndPublish">
                                <x-loading-animation wire:target="payAndPublish" />
                                {{ __('messages.t_proceed_with_payment') }}</x-button.secondary>
                            @else
                                <x-button.primary class="min-w-[6rem] w-full md:w-auto" wire:click="publish"
                                    x-bind:disabled="{{ json_encode($isDisabled) }}"
                                    style="{{ $isDisabled ? 'cursor: not-allowed;' : '' }}">
                                    <x-loading-animation wire:target="publish" />
                                    {{ $ad && $ad->status->value != 'draft' ? __('messages.t_preview_ad') : __('messages.t_publish') }}
                                    </x-button.secondary>
                        @endif
                    @else
                        <x-button.primary id="next" class="min-w-[6rem] w-full md:w-auto" wire:click="next"
                            x-bind:disabled="{{ json_encode($isDisabled) }}"
                            style="{{ $isDisabled ? 'cursor: not-allowed;' : '' }}">
                            <x-loading-animation wire:click="next" />{{ __('messages.t_next') }}
                        </x-button.primary>
                    @endif
                </div>
            @endif
        </div>
    </div>
</header>
