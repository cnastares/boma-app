<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('messages.t_secure_area_notification') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div x-data="{ isPasswordRevealed: false }">
            <x-input-label for="password" :value="__('messages.t_password')" />

            <div class="mt-1">
                <x-filament::input.wrapper class="mt-1">
                    <x-filament::input id="password" name="password" x-bind:type="isPasswordRevealed?'text':'password'"
                        autocomplete="current-password" required />
                        <x-slot name="suffix">
                            <button type="button" @click="isPasswordRevealed = ! isPasswordRevealed"
                                class="cursor-pointer block"
                                :aria-label="isPasswordRevealed ? '{{__('messages.t_aria_label_hide')}}' : '{{__('messages.t_aria_label_show')}}'"
                                :aria-pressed="isPasswordRevealed.toString()">
                                <div x-cloak x-show="isPasswordRevealed" x-tooltip="{
                                    content: '{{__('messages.t_tooltip_hide')}}',
                                    theme: $store.theme,
                                }">
                                    <x-heroicon-s-eye-slash class="w-5 h-5 text-gray-700" aria-hidden="true" />
                                </div>
                                <div x-cloak x-show="!isPasswordRevealed"
                                x-tooltip="{
                                    content: '{{__('messages.t_tooltip_show')}}',
                                    theme: $store.theme,
                                }">
                                    <x-heroicon-s-eye class="w-5 h-5 text-gray-700" aria-hidden="true" />
                                </div>
                            </button>
                        </x-slot>
                </x-filament::input.wrapper>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('messages.t_confirm_action') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
