@props([
    'imageUrl' => '/images/not-found.svg',
    'description' => 'No items available.',
    'size' => 'md' // Default size
])

@php
    $sizes = [
        'sm' => [
            'imageSize' => 'w-30',
            'descriptionSize' => 'text-lg',
        ],
        'md' => [
            'imageSize' => 'w-40',
            'descriptionSize' => 'text-xl',
        ],
        'lg' => [
            'imageSize' => 'w-60',
            'descriptionSize' => 'text-2xl',
        ],
    ];

    $imageSize = $sizes[$size]['imageSize'] ?? $sizes['md']['imageSize'];
    $descriptionSize = $sizes[$size]['descriptionSize'] ?? $sizes['md']['descriptionSize'];
@endphp

<div class="flex flex-col items-center justify-center  font-semibold ">
    <img src="{{ asset($imageUrl) }}" alt="Not Found" class="{{ $imageSize }} mb-4" />
    <p class="text-center !text-base {{ $descriptionSize }}">{{ $description }}</p>
</div>
