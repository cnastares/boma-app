@props(['message', 'active' => false])
@php
    $imageProperties = $message->conversation?->ad?->image_properties;
    $altText = $imageProperties['1'] ?? $message->conversation?->ad?->title;
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start p-4 border-b border-gray-200 classic:border-black dark:border-white/10' . ($active ? ' md:bg-gray-100 dark:md:bg-gray-800  ' : '')]) }} >

    <img src="{{ $message->conversation?->ad?->primaryImage ? asset($message->conversation?->ad?->primaryImage?->image_path) : asset('images/placeholder.jpg') }}" alt="{{ $altText }}" class="w-12 h-12 rounded-xl object-cover">
    <div class="ml-3 flex-grow">
        <div class="relative justify-between pr-10" x-data>
            <h3 class="text-base line-clamp-1">{{ $message->conversation?->ad?->title }}</h3>
            <div class="absolute right-0 -top-2.5" @click.stop="">{{ ($this->delete)(['conversation' => $message->conversation]) }}</div>
        </div>
        <div class="flex justify-between mt-2 dark:text-gray-100 {{ $active ? 'text-gray-600 ' : 'text-gray-600' }}" >
            <span>
                {{ auth()->user()->id == $message->sender->id ? $message->receiver?->name : $message->sender?->name }}
            </span>
            <span class="text-sm">{{ $message->created_at->format('d/m/Y') }}</span>
        </div>
    </div>
</div>

