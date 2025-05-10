<x-filament-panels::page class="">
    <style>
        :root {
            --primary: {{ $appearanceSettings?->primary_color ?? 'rgba(253, 174, 75, 1)' }};
        }

        .bg-price-gradient {
            background-image: linear-gradient(to left, transparent 1em, var(--primary) 1em);
        }

        .post-ad {
            background-color: black;
        }
    </style>
    <div x-data="{ isOpen: false }" x-init="function() {
        const urlParams = new URLSearchParams(window.location.search);
        isOpen = urlParams.has('conversation_id');
    }" class="">
        <div
            class="grid md:grid-cols-10 bg-white rounded-none md:rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  classic:ring-black">
            <!-- Left Section -->
            <div class="col-span-full md:col-span-3  md:border-r border-gray-200 classic:border-black dark:border-white/10 "
                :class="isMobile && isOpen ? 'hidden md:block' : ''">
                @if ($messages->isEmpty())
                    <div class="my-10">
                        <x-not-found description="{{ __('messages.t_no_messages_found') }}" />
                    </div>
                @else
                    <!-- Header -->
                    <div
                        class="hidden md:flex justify-between items-center  px-4 py-6 border-b border-gray-200  classic:border-black dark:border-white/10">
                        <h1 class="text-xl font-semibold">{{ __('messages.t_my_messages') }}</h1>
                    </div>
                    <!-- List of Messages -->
                    <div class="md:max-h-[25rem] md:overflow-y-auto">
                        @foreach ($messages as $message)
                            <div wire:key='message-{{ $message->id }}'
                                @click="
                                isMobile = window.innerWidth < 1024;
                                isOpen = true;
                                $wire.openMessage({{ $message }}, isMobile);
                                "
                                class="relative">
                                <livewire:user.message-item wire:key='message-{{ $message->id }}' :message="$message"
                                    :active="$message->id == $activeMessageId" lazy />
                                <div class="absolute right-2 top-[0.5rem]" @click.stop="">
                                    {{ ($this->delete)(['conversation' => $message->conversation]) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <x-filament-actions::modals />
                @endif
            </div>

            <!-- Right Section -->
            <div class="col-span-full md:col-span-7" :class="isMobile && isOpen ? 'mobile-class' : 'hidden md:block'">
                @if (isset($conversation_id))
                    <livewire:user.message-detail :conversation_id="$conversation_id" lazy>
                    @else
                        <div class="my-10">
                            <x-not-found description="{{ __('messages.t_no_conversation_selected') }}" />
                        </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
