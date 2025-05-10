<div>

    <livewire:layout.header isMobileHidden lazy />
    <x-page-header title="{{ __('messages.t_contact_us') }}" isMobileHidden :$referrer />

    <div class=" pt-8 pb-24 md:py-16">
        <div class="container mx-auto px-4">
            <div class=" grid grid-cols-1 gap-6 justify-between md:flex md:gap-12">
                <div class="max-w-lg space-y-3">
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        {{ __('messages.t_how_may_we_assist') }}
                    </h1>
                    <p>
                        {{ __('messages.t_available_to_support') }}
                    </p>
                    <div>
                        <ul class="mt-6 flex flex-wrap gap-x-10 gap-y-6 items-center">
                            @if($generalSettings->contact_email)
                                <li class="flex items-center gap-x-3">
                                    <div class="flex-none text-gray-400">
                                        <x-heroicon-o-envelope class="w-6 h-6" />
                                    </div>
                                    <p>
                                        {{ $generalSettings->contact_email }}
                                    </p>
                                </li>
                            @endif

                            @if($generalSettings->contact_phone)
                                <li class="flex items-center gap-x-3">
                                    <div class="flex-none text-gray-400">
                                        <x-heroicon-o-phone class="w-6 h-6" />
                                    </div>
                                    <p>
                                        {{ $generalSettings->contact_phone }}
                                    </p>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="flex-1 sm:max-w-lg lg:max-w-md">
                    <form wire:submit.prevent="sendMessage" class="space-y-5">
                       {{ $this->form }}
                       <div class="mt-10">
                           <x-filament::button wire:click="sendMessage" size="lg" class="w-full">
                                {{ __('messages.t_send_message') }}
                            </x-filament::button>
                       </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <livewire:layout.footer />

    <livewire:layout.bottom-navigation />
</div>
