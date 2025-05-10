@if (isEnablePointSystem())
    @auth
        <p class="text-xs text-green-600 balance-container">
            <span class="balance">{{ __('messages.t_balance') }}:</span>
            <span class="balance-value">{{ number_format(auth()->user()->wallet?->points ?? 0) }}</span>
            <span class="balance-currency">{{ getPointSystemSetting('short_name') }}</span>
        </p>
        <p class="text-xs text-amber-600 balance-container">
            <span class="balance">{{ __('messages.t_on_hold') }}:</span>
            <span class="balance-value">{{ number_format(auth()->user()->wallet?->points_on_hold ?? 0) }}</span>
            <span class="balance-currency">{{ getPointSystemSetting('short_name') }}</span>
        </p>
    @endauth
@endif
