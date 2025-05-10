<div>
    {{-- header section  --}}
    <livewire:layout.header context="home" lazy />

    <div class=" container mx-auto px-4 flex flex-col items-center justify-center min-h-screen md:min-h-full md:mt-20 md:mb-60">
        <!-- Success Image -->
        <img src="{{ asset('/images/tick.svg') }}" alt="success" class="w-16 h-16 mb-4">

        <!-- Congratulations Text -->
        <h2 class="text-xl pt-1 pb-3 font-semibold">{{ __('messages.t_order_confirmation_title') }}</h2>
        <!-- If the ad needs admin approval -->
        <p class="text-center mb-6">{{ __('messages.t_order_confirmation_description') }}</p>
        <div class="flex justify-center gap-x-5 mb-6">
            <x-filament::button href="{{ route('reservation.my-purchases') }}" tag="a" outlined class=" cursor-pointer">{{ __('messages.t_order_confirmation_view_my_order') }}</x-filament::button>
            <x-filament::button onclick="window.location='/'" color="gray">{{ __('messages.t_order_confirmation_back_to_home') }}</x-filament::button>
        </div>
    </div>
</div>