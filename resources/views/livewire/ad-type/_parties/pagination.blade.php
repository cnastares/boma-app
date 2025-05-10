<div
    class="sm:flex-1 sm:flex sm:items-center sm:justify-between mt-10 mb-10 md:mb-0  {{ isMapViewEnabled() ? 'p-2' : '' }}">
    <div>
        <p class="text-base text-slate-700 leading-5 dark:text-slate-400 ">
            <span>{{ __('messages.t_showing_results') }}</span>
            <span class="font-medium">{{ ($ads->currentPage() - 1) * $ads->perPage() + 1 }}</span>
            <span>{{ __('messages.t_to') }}</span>
            <span
                class="font-medium">{{ min($ads->currentPage() * $ads->perPage(), $ads->count()) }}</span>
            <span>{{ __('messages.t_of') }}</span>
            <span class="font-medium">{{ $ads->count() }}</span>
            <span>{{ __('messages.t_results_count') }}</span>

        </p>
    </div>

    <div>
        {{ $ads->links() }}
    </div>
</div>