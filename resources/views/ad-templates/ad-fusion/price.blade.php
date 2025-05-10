<div class="text-sm font-semibold" itemprop="price" content="1">
    <!-- Work when price selected -->
    @if ($type_id == 1)
        <!-- If offer is enabled -->
        @if ($offer_enabled && $offer_price )
            <span class="line-through  text-black/50 dark:text-gray-500">
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
            @if ($offer_percentage )
                <span class="bg-green-100 text-green-600 px-1.5 rounded-md">{{ $offer_percentage }}%</span>
            @endif
        @else
        <span>
            {{ $ad_type == POINT_SYSTEM_MARKETPLACE ? currencyToPointConversion($value) : $value }}
                <!-- Price suffix -->
            @if (($has_prefix && $price_suffix))
                / {{ $price_suffix }}
            @endif
        </span>
        @endif
    @else
    <span>
        {{ $label }}
    </span>
    @endif
</div>
