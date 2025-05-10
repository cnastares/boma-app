<x-auth-layout>

    <!-- Header -->
    <header class="flex justify-between items-center mb-6">
        <x-brand />
        <a href="/login" class="underline text-lg">Login</a>
    </header>

    <main class=" min-h-full flex items-center" id="main-content">
        <div class="mt-[-3rem]">
            <h1 class="text-2xl font-bold mb-6 text-center">
                {{ __('messages.t_forgot_password_title') }}
            </h1>

            <div class="mb-4">
                {{ __('messages.t_forgot_password_description') }}
            </div>


            <!-- Session Status -->
            <x-auth-session-status class="rounded-xl bg-green-50 p-4 mb-4 border-green-600" :status="session('status')" />

            <!-- Validation Errors -->
            <x-auth-validation-errors class="rounded-xl bg-red-50 p-4 mb-4 border-red-600" :errors="$errors" />

            <div class="mt-6">
                <form method="POST" action="{{ route('password.email') }}" x-data="{
                    email:{{!is_null(old('email')) ? json_encode(old('email')) : 'null'}},
                     }">
                    @csrf
                    <!-- Email Address -->
                    <div>
                        <x-label for="email" :value="__('messages.t_email')" />

                        <x-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required autofocus  x-model="email" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-button.secondary size="lg" class="block w-full dark:!bg-primary-600" x-bind:disabled="!email">
                            {{ __('messages.t_email_password_reset_link') }}
                        </x-button.secondary>
                    </div>
                </form>
            </div>
        </div>
    </main>
</x-auth-layout>
