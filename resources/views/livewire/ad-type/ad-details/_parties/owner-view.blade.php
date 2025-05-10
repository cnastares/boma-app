@if ($ownerView)
<div class="flex items-center p-4 mb-8 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800"
    role="alert">

    <x-heroicon-o-exclamation-circle class="w-6 h-6" />

    <div class="ml-3 text-sm font-medium">
        @if ($this->ad->status->value == 'expired')
        {{ __('messages.t_listing_expired', [
                                'title' => $this->ad->title,
                                'date' => $this->ad->expires_at->format('F j, Y'),
                            ]) }}
        @elseif($this->ad->status->value == 'inactive')
        {{ __('messages.t_item_deactivated', ['title' => $this->ad->title]) }}
        @else
        {{ __('messages.t_item_marked_as_status', [
                                'title' => $this->ad->title,
                                'status' => __('messages.t_' . $this->ad->status->value . '_status'),
                            ]) }}
        @endif
        <a href="{{ route('post-ad') }}"
            class="font-semibold underline hover:no-underline">{{ __('messages.t_post_new_ad') }}</a>.
    </div>
</div>
@endif