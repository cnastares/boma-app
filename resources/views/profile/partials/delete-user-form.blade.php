<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6" x-data="{ isPasswordRevealed: false }">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <div class="mt-1">
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input id="password" name="password" x-bind:type="isPasswordRevealed?'text':'password'"
                        placeholder="{{ __('Password') }}"
                            />
                            <x-slot name="suffix">
                                <button
                                    :aria-label="isPasswordRevealed ? '{{__('messages.t_aria_label_hide')}}' : '{{__('messages.t_aria_label_show')}}'"
                                    :aria-pressed="isPasswordRevealed.toString()"
                                    type="button" @click="isPasswordRevealed = ! isPasswordRevealed"
                                    class="cursor-pointer block">
                                    <div x-cloak x-show="isPasswordRevealed" x-tooltip="{
                                        content: '{{__('messages.t_tooltip_hide')}}',
                                        theme: $store.theme,
                                    }">
                                        <x-heroicon-s-eye-slash class="w-5 h-5 text-gray-700" />
                                    </div>
                                    <div x-cloak x-show="!isPasswordRevealed"
                                    x-tooltip="{
                                        content: '{{__('messages.t_tooltip_show')}}',
                                        theme: $store.theme,
                                    }">
                                        <x-heroicon-s-eye class="w-5 h-5 text-gray-700" />
                                    </div>
                                </button>
                            </x-slot>
                    </x-filament::input.wrapper>
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
