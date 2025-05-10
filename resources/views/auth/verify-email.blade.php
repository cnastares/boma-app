<x-auth-layout>
    <div class="flex items-center h-full">
        <div>
            <!-- Validation Errors -->
            <x-auth-validation-errors class="rounded-xl bg-red-50 p-4 mb-4 border border-red-600" :errors="$errors" />

            <div class="items-center flex justify-center mb-10">
                <x-brand />
            </div>

            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                {{ __('messages.t_signup_thanks') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ __('messages.t_verification_link_sent') }}
                </div>
            @endif

            <main class="mt-4 flex items-center justify-between">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <div>
                        <x-primary-button>
                            {{ __('messages.t_resend_verification_email') }}
                        </x-primary-button>
                    </div>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        {{ __('messages.t_logout') }}
                    </button>
                </form>
            </main>
        </div>
    </div>
</x-auth-layout>
