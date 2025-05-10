@props([
    'id' => null,
    'title' => 'Modal',
    'closeByEscaping' => true,
    'closeByClickingAway' => true,
    'maxWidth' => '5/6',
    'trigger' => null, // Slot for button trigger
    'slideOver' => false,
    'width'=>'2xl'
])

@php
$modalWidths = [
    'sm' => 'w-[16rem]',    // 16rem (256px)
    'md' => 'w-[18rem]',    // 18rem (288px)
    'lg' => 'w-[20rem]',    // 20rem (320px)
    'xl' => 'w-[24rem]',    // 24rem (384px)
    '2xl' => 'w-[28rem]',   // 28rem (448px)
    '3xl' => 'w-[32rem]',   // 32rem (512px)
    '4xl' => 'w-[36rem]',   // 36rem (576px)
    '5xl' => 'w-[42rem]',   // 42rem (672px)
    '6xl' => 'w-[48rem]',   // 48rem (768px)
    '7xl' => 'w-[56rem]',   // 56rem (896px)
    '8xl' => 'w-[64rem]',   // 64rem (1024px)
    '9xl' => 'w-[72rem]',   // 72rem (1152px)
    '10xl' => 'w-[80rem]',  // 80rem (1280px)
];

@endphp
<div x-data="{
    isOpen: false,
    open: function () {
        this.$nextTick(() => {
            this.isOpen = true;
            {{-- this.$refs.modalContainer.dispatchEvent(
                new CustomEvent('modal-opened', { detail: { id: '{{ $id }}' } })
            ); --}}
        });
    },
    close: function () {
        this.isOpen = false;
        {{-- this.$refs.modalContainer.dispatchEvent(
            new CustomEvent('modal-closed', { detail: { id: '{{ $id }}' } })
        ); --}}
    },
}"
    x-trap.noscroll="isOpen"
    x-transition:enter="duration-300"
    x-transition:leave="duration-300"
    x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') open()"
    x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') close()"
    x-on:keydown.escape.window="if (isOpen && {{ $closeByEscaping ? 'true' : 'false' }}) isOpen = false"
    class="relative">

    {{-- Trigger Button (if provided) --}}
    @if ($trigger)
    <div x-on:click="isOpen = true" class="cursor-pointer">
        {{ $trigger }}
    </div>
    @endif

    {{-- Modal Overlay --}}
    <div x-show="isOpen" x-cloak x-transition.opacity
        class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center  {{ $slideOver ? 'justify-end' : 'justify-center' }}"
        @if($closeByClickingAway)
            x-on:click.self="isOpen = false"
        @endif
        aria-label="{{ $id }}-{{ $title }}" role="dialog"
        aria-modal="true"
        >

        {{-- Modal Content --}}
        <div x-show="isOpen"
        @if ($slideOver)
        x-transition:enter-start="translate-x-full rtl:-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full rtl:-translate-x-full"
        @else
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave-start="scale-100 opacity-100"
            x-transition:leave-end="scale-95 opacity-0"
        @endif
            class="bg-white dark:bg-gray-900 shadow-lg overflow-x-auto max-w-{{ $maxWidth }}  {{ $slideOver ? 'h-full absolute right-0 top-0 w-full sm:w-[50%]' : 'relative mx-4 rounded-lg max-h-[85%]'  }} {{ isset($modalWidths[$width]) ? $modalWidths[$width]:''}}">

            {{-- Modal Header --}}
            <div class="flex flex-col p-4 border-b dark:border-gray-700">
                <!-- Header Section -->
                <div class="flex items-center justify-between ">
                    <!-- Heading Slot -->
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $heading ?? '' }}
                    </div>

                    <!-- Close Button -->
                    <button type="button" x-on:click="isOpen = false"
                        class="p-2 rounded-full text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700  dark:focus:ring-gray-600"
                        aria-label="{{ __('messages.t_aria_label_close') }}">
                        <x-heroicon-o-x-mark class="w-6 h-6" aria-hidden="true" />
                    </button>
                </div>

                <!-- Description Slot -->
                @if(!empty($description))
                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $description }}
                    </div>
                @endif
            </div>

            {{-- Modal Body --}}
            <div class="p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
