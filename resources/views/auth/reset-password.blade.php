<x-auth-layout>
    <div class="flex items-center h-full w-full">
        <div class="w-full">
            <header class="items-center flex justify-center mb-10">
                <x-brand />
            </header>

            <main id="main-content">
                <h1 class="text-2xl font-bold text-center mb-6">
                    {{ __('messages.t_reset_your_password') }}
                </h1>

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('messages.t_email')" />
                        <div class="mt-1">
                            <x-filament::input.wrapper class="mt-1">
                                <x-filament::input id="email" type="text" name="email"
                                    :value="old('email', $request->email)" autocomplete="email" required autofocus />
                            </x-filament::input.wrapper>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4" x-data="{ isPasswordRevealed: false }">
                        <x-input-label for="password" :value="__('messages.t_password')" />
                        <div class="mt-1">
                            <x-filament::input.wrapper class="mt-1">
                                <x-filament::input id="password" name="password"
                                    x-bind:type="isPasswordRevealed?'text':'password'" autocomplete="new-password"
                                    required />
                                <x-slot name="suffix">
                                    <button
                                        :aria-label="isPasswordRevealed ? '{{__('messages.t_aria_label_hide')}}' : '{{__('messages.t_aria_label_show')}}'"
                                        :aria-pressed="isPasswordRevealed.toString()" type="button"
                                        @click="isPasswordRevealed = ! isPasswordRevealed" class="cursor-pointer block">
                                        <div x-cloak x-show="isPasswordRevealed" x-tooltip="{
                                            content: '{{__('messages.t_tooltip_hide')}}',
                                            theme: $store.theme,
                                        }">
                                            <x-heroicon-s-eye-slash class="w-5 h-5 text-gray-700" aria-hidden="true" />
                                        </div>
                                        <div x-cloak x-show="!isPasswordRevealed" x-tooltip="{
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

                    <!-- Confirm Password -->
                    <div class="mt-4" x-data="{ isPasswordRevealed: false }">
                        <x-input-label for="password_confirmation" :value="__('messages.t_confirm_password')" />
                        <div class="mt-1">
                            <x-filament::input.wrapper class="mt-1">
                                <x-filament::input id="password_confirmation"
                                    x-bind:type="isPasswordRevealed?'text':'password'" name="password_confirmation"
                                    required autocomplete="new-password" />
                                <x-slot name="suffix">
                                    <button
                                        :aria-label="isPasswordRevealed ? '{{__('messages.t_aria_label_hide')}}' : '{{__('messages.t_aria_label_show')}}'"
                                        :aria-pressed="isPasswordRevealed.toString()" type="button"
                                        @click="isPasswordRevealed = ! isPasswordRevealed" class="cursor-pointer block">
                                        <div x-cloak x-show="isPasswordRevealed" x-tooltip="{
                                            content: '{{__('messages.t_tooltip_hide')}}',
                                            theme: $store.theme,
                                        }">
                                            <x-heroicon-s-eye-slash class="w-5 h-5 text-gray-700" />
                                        </div>
                                        <div x-cloak x-show="!isPasswordRevealed" x-tooltip="{
                                            content: '{{__('messages.t_tooltip_show')}}',
                                            theme: $store.theme,
                                        }">
                                            <x-heroicon-s-eye class="w-5 h-5 text-gray-700" />
                                        </div>
                                    </button>
                                </x-slot>
                            </x-filament::input.wrapper>
                        </div>

                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button>
                            {{ __('Reset Password') }}
                        </x-primary-button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</x-auth-layout>
