@php
    $id = $getId();
    $isDisabled = $isDisabled();
    $size = $getSize();
    $sizeClass = match ($size) {
        'xs' => 'w-3 h-3',
        'sm' => 'w-4 h-4',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-10 h-10',
    };
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{
        rating: @entangle($getStatePath()),
        hoverRating: 0,
        setRating(amount) {
            this.rating = this.rating === amount ? 0 : amount;
        }
    }" class="flex gap-1">
        @foreach ($getStarsArray() as $value)
            <button
                type="button"
                @click="setRating({{ $value }})"
                @mouseover="hoverRating = {{ $value }}"
                @mouseleave="hoverRating = 0"
                class="w-8 h-8 p-0.5 m-0 text-gray-400 cursor-pointer "
                :style="hoverRating >= {{ $value }} || rating >= {{ $value }} ? 'color:#FFC52F' : 'color: rgba(var(--gray-400), var(--tw-text-opacity));'"
                title="{{ $value }}"
                wire:loading.attr="disabled"
                @disabled($isDisabled)
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                    class="w-full h-full transition duration-150">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                </svg>
            </button>
        @endforeach
    </div>
</x-dynamic-component>
