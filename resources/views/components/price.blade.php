@props([
    'value',
    'type_id',
    'label',
    'has_prefix',
    'price_suffix',
    'offer_enabled',
    'offer_price',
    'offer_percentage',
    'ad_type'
])

<div class="price bg-price-gradient block p-1 pr-[calc(0.5rem+1em)] text-black relative classic:border classic:border-r-0 classic:border-black overflow-hidden whitespace-nowrap truncate leading-[1.1rem] text-sm"
    itemprop="price" content="1">
    @if ($type_id == 1)
        <!-- If offer is enabled -->
        @if ($offer_enabled && $offer_price)
            <span class="line-through font-normal text-black/50 dark:text-gray-500">
                {{ $ad_type == POINT_SYSTEM_MARKETPLACE ? currencyToPointConversion($value) : $value }}
            </span>
            <span>
                {{ $ad_type == POINT_SYSTEM_MARKETPLACE ? currencyToPointConversion($offer_price) : $offer_price }}
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
            {{ $ad_type == POINT_SYSTEM_MARKETPLACE ? currencyToPointConversion($value) : $value }}
            @if ($has_prefix && $price_suffix)
                / {{ $price_suffix }}
            @endif
        @endif
    @else
        {{ $label }}
    @endif
</div>
