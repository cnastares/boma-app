<div
    class="flex items-center bg-gray-50 mt-4 cursor-pointer justify-between rounded-xl w-full py-1 px-2 border border-gray-200 dark:border-white/20 dark:bg-gray-900 classic:border-black"
    x-data="{ revealed: @entangle('revealed') }"
>
    <!-- Phone Icon -->
    <div class="p-2 flex gap-x-2 items-center">
        <x-heroicon-o-phone class="w-5 h-5" />
        <!-- Phone Number Display -->
        <div class="font-medium">
            <template x-if="!revealed">
                <span>{{ substr($phoneNumber, 0, -6) . 'XXXXXX' }}</span>
            </template>
            <a target="_blank" href="tel:{{ $phoneNumber }}" class="flex items-center gap-2">
                <template x-if="revealed">
                    <span>{{ $phoneNumber }}</span>
                </template>
            </a>
        </div>
    </div>

    <!-- Reveal Button -->
    <button type="button"
        x-show="!revealed"
        class="px-3 py-1 text-sm text-primary-600 underline"
        @click="$wire.revealContact()">
        {{ __('messages.t_reveal') }}
    </button>
</div>
