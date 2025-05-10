@props([
    'value',
    'type_id',
    'label',
    'has_prefix',
    'price_suffix',
    'offer_enabled',
    'offer_price',
    'offer_percentage',
])

<div class="block dark:border-white/20 classic:border-black pr-[calc(0.5rem+1em)] text-black relative overflow-hidden whitespace-nowrap truncate leading-[1.1rem] text-sm"
    itemprop="price" content="1">
    <h2 class="ltr:p-4 md:text-3xl text-xl md:pl-0 pl-3 font-semibold md:block dark:text-white">
    @if ($type_id == 1)
        <!-- If offer is enabled -->
        @if ($offer_enabled && $offer_price)
            <span class="line-through font-normal text-black/50 dark:text-gray-500">{{ $value }}</span>
            <span>
                {{ $offer_price }}
                <!-- Price suffix -->
                @if ($has_prefix && $price_suffix)
                    / {{ $price_suffix }}
                @endif
            </span>
            <!-- Offer Percentage -->
            @if ($offer_percentage)
                <span class="bg-primary-100  px-1.5 rounded-md">{{ $offer_percentage }}%</span>
            @endif
        @else
            {{ $value }} @if ($has_prefix && $price_suffix)
                / {{ $price_suffix }}
            @endif
        @endif
    @else
        {{ $label }}
    @endif
    </h2>
</div>
