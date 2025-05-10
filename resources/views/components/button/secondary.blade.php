@props(['disabled' => false])

<x-button :disabled="$disabled" {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-black dark:bg-white/10 border-black text-white hover:bg-gray-700 ']) }}>
    {{ $slot }}
</x-button>
