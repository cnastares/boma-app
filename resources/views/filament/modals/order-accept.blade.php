<div class="fi-modal-description text-sm text-gray-500 dark:text-gray-400 text-center">
    @if ($record->order_type == RESERVATION_TYPE_POINT_VAULT && getPointSystemSetting('policy_page'))
    <p>
        {{ __('messages.t_my_sale_for_more_details_para') }}
        <a href="{{ $slug ? route('page-details', $slug) : '/' }}" target="_blank" class="text-blue-500 underline">
            {{ __('messages.t_my_sale_for_more_details_link') }}
        </a>
    </p>
    @endif
</div>