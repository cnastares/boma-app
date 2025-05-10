<x-filament::page>
    <div class="flex items-center justify-between">
        <div class="w-full mr-2">
            {{ $this->search }}
        </div>
        <div class="w-auto ml-2">
            <x-filament::button
                    x-on:click="window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'filament-log-manager-delete-log-file-modal' } }));"
                    :disabled="is_null($this->logFile)"
                    type="button"
                    color="danger"
            >
                {{ __('messages.t_ap_delete') }}
            </x-filament::button>
        </div>
        <div class="w-auto ml-2">
            <x-filament::button
                    wire:click="download"
                    :disabled="is_null($this->logFile)"
                    type="button"
                    color="primary"
            >
                {{ __('messages.t_ap_download') }}
            </x-filament::button>
        </div>
    </div>
    <hr>
    <div>
        <div>
            <div x-data="{ isCardOpen: null }" class="flex flex-col">
                @forelse($this->getLogs() as $key => $log)
                    <div
                            class="rounded-xl relative mb-2 py-3 px-3 bg-{{ $log['level_class'] }}"
                            :class="{'no-bottom-radius mb-0': isCardOpen == {{$key}}}"
                    >
                        <a
                                @click="isCardOpen = isCardOpen == {{$key}} ? null : {{$key}} "
                                style="cursor: pointer;"
                                class="block overflow-hidden rounded-t-xl text-white"
                        >
                            <span>[{{ $log['date'] }}]</span>
                            {{ Str::limit($log['text'], 100) }}
                        </a>
                    </div>
                    <div x-show="isCardOpen=={{$key}}" class="mb-2 px-4 py-4 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl no-top-radius">
                        <div>
                            <p>{{$log['text']}}</p>
                            @if(!empty($log['stack']))
                                <div class="bg-gray-100 dark:bg-gray-900 mt-4 p-4 text-sm opacity-40">
                                    <pre style="overflow-x: scroll;"><code>{{ trim($log['stack']) }}</code></pre>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <h3 class="text-center">{{ __('messages.t_ap_no_logs') }}</h3>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal -->
    <x-filament::modal id="filament-log-manager-delete-log-file-modal">
        <x-slot name="heading">
            {{ __('messages.t_ap_delete_logs_file') }}
        </x-slot>
        <x-slot name="description">
            {{ __('messages.t_ap_delete_logs_confirmation') }}
        </x-slot>
        <x-slot name="footerActions">
            <x-filament::button
                    type="button"
                    x-on:click="isOpen = false"
                    color="secondary"
                    outlined="true"
                    class="filament-page-modal-button-action"
            >
                {{ __('messages.t_ap_cancel') }}
            </x-filament::button>
            <x-filament::button
                    wire:click="delete"
                    x-on:click="isOpen = false"
                    type="button"
                    color="danger"
                    class="filament-page-modal-button-action"
            >
                {{ __('messages.t_ap_delete') }}
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</x-filament::page>
