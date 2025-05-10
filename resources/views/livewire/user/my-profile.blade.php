<div>
    <!-- Skip links -->
    @include('components.skip-links',['links'=>[
        'main-content'=> __('messages.t_skip_to_main_content')
    ]])

    <livewire:layout.header isMobileHidden lazy />
    <x-page-header title="{{ __('messages.t_edit_profile') }}" isMobileHidden :$referrer />

    <x-user-navigation />

    <!-- Main content -->
    <main id="main-content" class="sticky-scroll-margin" >
    <div class="container mx-auto px-4 py-10">
        @if (isEnablePointSystem())
        <div class=" rounded-xl py-3 mt-4 mb-5 bg-white md:shadow-sm  ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  classic:ring-black">
            <div class="px-6 flex justify-between">
                <h2 class="text-xl font-semibold  text-[#3E7B27]">{{ __('messages.t_balance_point') }}: {{ auth()->user()->wallet?->points ?? 0 }} {{getPointSystemSetting('short_name') }}</h2>
                <a href="{{ route('point-vault.buy-point') }}" class="underline cursor-pointer">{{ __('messages.t_buy_point') }}</a>
            </div>
            <div class="px-6 ">
                <p class="text-base text-gray-900 dark:text-gray-200 text-wrap mt-3 w-[90%]">{{ __('messages.t_balance_point_description') }}</p>
            </div>
        </div>
        @endif
        <div class="rounded-xl md:bg-white md:shadow-sm  md:ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  classic:ring-black">
            <div class="justify-between px-6 py-3 hidden md:flex border-b border-gray-200  classic:border-black dark:border-white/10 ">
                <h1 class="text-xl font-semibold">{{ __('messages.t_edit_profile') }}</h1>
                <a href="{{ route('view-profile', ['id' => auth()->id(), 'slug' => $user->slug]) }}" class="underline">{{ __('messages.t_view_profile') }}</a>
            </div>
            <div class="px-6 pb-3  md:hidden flex justify-end classic:border-black dark:border-white/10 ">
                <a href="{{ route('view-profile', ['id' => auth()->id(), 'slug' => $user->slug]) }}" class="underline">{{ __('messages.t_view_profile') }}</a>
            </div>
            <form wire:submit="create" novalidate>
                <div class=" pb-14 md:px-6 md:py-8">
                    <div>
                        {{ $this->form }}
                    </div>
                </div>
                <div class="px-6 py-4 bg-white rounded-b-xl fixed md:static bottom-0 left-0 right-0 z-10 text-right border-t border-gray-200 classic:border-black dark:border-white/10 dark:bg-gray-900">
                    <x-button.secondary type="submit" size="lg" class="w-full md:w-auto min-w-[10rem] dark:!text-black dark:!bg-primary-600"
                        x-bind:disabled="{{$isDisabled}}">{{ __('messages.t_save_changes') }}</x-button.secondary>
                </div>
            </form>
            <x-filament-actions::modals />
        </div>

        <div class=" rounded-xl py-3 mt-4 mb-5 bg-white md:shadow-sm  ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  classic:ring-black">
            <div class="px-6 flex justify-between">
                <h2 class="text-xl font-semibold">{{ __('messages.t_permanently_delete_profile') }}</h2>
                <x-filament::button color="danger" @click="$dispatch('open-modal', { id: 'edit-user' })" icon="heroicon-m-trash">
                    {{ __('messages.t_delete_profile') }}
                </x-filament::button>
            </div>
            <div class="px-6 ">
                <p class="text-base text-gray-900 dark:text-gray-200 text-wrap mt-3">{{ __('messages.t_permanently_delete_profile_content') }}</p>
            </div>
        </div>
    </div>
    </main>
    <x-modal.index id="edit-user" width='2xl'>
        <x-slot name="heading">
            <h2 class="text-xl font-semibold">{{ __('messages.t_delete_profile') }}</h2>
        </x-slot>

        <x-slot name="description">
            <p class="text-base text-gray-900 dark:text-gray-200 mt-3">{{ __('messages.t_permanently_delete_profile_content') }}</p>
        </x-slot>
            <x-filament::button wire:click="deleteMyAccount()" color="danger" icon="heroicon-m-trash">
                {{ __('messages.t_permanently_delete_profile') }}
            </x-filament::button>
    </x-modal.index>

    <!-- Todo: Enable point system -->
    {{-- <x-filament::modal id="buy-point" width='xl'>
        <x-slot name="heading">
            <h2 class="text-xl font-semibold">{{ __('messages.t_model_buy_point_title') }}</h2>
        </x-slot>
        <x-slot name="description">
            <p class="text-base text-gray-900 dark:text-gray-200 mt-3">{{ __('messages.t_permanently_delete_profile_content') }}</p>
        </x-slot>
        <x-slot name="footer">
            <x-filament::button wire:click="deleteMyAccount()" color="primary" icon="heroicon-m-rocket-launch">
                {{ __('messages.t_buy_point') }}
            </x-filament::button>
            <x-filament::button wire:click="deleteMyAccount()" color="danger" icon="heroicon-m-trash">
                {{ __('messages.t_cancel') }}
            </x-filament::button>
        </x-slot>
    </x-filament::modal> --}}
</div>
