
<div tabindex="0" class="cursor-pointer flex items-start p-4 "

>
@php
    $imageProperties = $message->conversation?->ad?->image_properties;
    $altText = $imageProperties['1'] ?? $message->conversation?->ad?->title;
@endphp
    <img src="{{ $message->conversation?->ad?->primaryImage ?? asset('/images/placeholder.jpg') }}"  alt="{{ $altText }}" class="w-12 h-12 rounded-xl object-cover">
    <div class="ml-3 flex-grow">
        <div class="relative justify-between pr-10" x-data>
            @if ($message->conversation?->ad != null)
            <h2 class="text-base line-clamp-1">{{ $message->conversation?->ad?->title }}</h2>
            @else
            <h3 class="text-base line-clamp-1">{{__('messages.t_ad_not_found')}}</h3>
            @endif
            {{-- <h3 class="text-base line-clamp-1">{{ $message->conversation?->ad?->title }}</h3> --}}

        </div>
        <div class="flex justify-between mt-2 dark:text-gray-100 {{ $active ? 'text-gray-600 ' : 'text-gray-600' }}" >
            <span>
                {{ auth()->user()->id == $message?->sender?->id ? $message?->receiver?->name : $message?->sender?->name }}
            </span>
            <span class="text-sm">{{ $message->created_at->format('d/m/Y') }}</span>
        </div>
    </div>
</div>

