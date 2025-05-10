<x-modal.index id="report-ad" alignment="start" width="2xl">
    <x-slot name="heading">{{ __('messages.t_report_this_ad') }}</x-slot>
    <div>
        <form wire:submit="reportAd">
            {{ $this->form }}

            <div class="mt-4">
                <x-filament::button type="submit">
                    {{ __('messages.t_report_ad') }}
                </x-filament::button>
            </div>
        </form>
    </div>
</x-modal.index>
