<div>
    <!-- Skip links -->
    @include('components.skip-links',['links'=>[
        'main-content'=> __('messages.t_skip_to_main_content')
    ]])
    <livewire:layout.header lazy />

    <main id="main-content" class="flex flex-col items-center justify-center min-h-screen md:min-h-full md:mt-20">

        <!-- Verification Image -->
        <img src="{{ asset('/images/error.svg') }}" alt="Verification needed" class="w-16 h-16 mb-4">

        <!-- Combined Title -->
        <h1 class="text-center text-xl mb-2 font-semibold">{{ __('messages.t_verification_needed') }}</h1>

        <!-- Description -->
        <p class="text-center mb-6">{{ __('messages.t_verification_needed_description') }}</p>

        <div class="flex justify-center gap-x-4">
            <x-filament::button href="{{ route('home') }}" tag="a" color="gray">
                {{ __('messages.t_back_to_home') }}
            </x-filament::button>

            <x-filament::button href="{{ route('filament.app.pages.verification') }}" tag="a" outlined>
                {{ __('messages.t_go_to_verification') }}
            </x-filament::button>
        </div>
    </main>
</div>
