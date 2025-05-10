<div>
    <x-filament::dropdown placement="bottom-start" teleport>
        <x-slot name="trigger">
            <x-filament::button color="danger" icon="heroicon-m-chevron-down" icon-position="after">
                {{ __('messages.t_ap_cache') }}
            </x-filament::button>
        </x-slot>

        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item wire:click="clearCache" @click="$refs.panel.close(event)" icon="heroicon-o-server-stack">
                {{ __('messages.t_ap_clear_system_cache') }}
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item wire:click="clearViews" @click="$refs.panel.close(event)" icon="heroicon-o-view-columns">
                {{ __('messages.t_ap_clear_compiled_views') }}
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item wire:click="clearLogs" @click="$refs.panel.close(event)" icon="heroicon-o-document">
                {{ __('messages.t_ap_clear_log_files') }}
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item wire:click="updateLanguage" @click="$refs.panel.close(event)" icon="heroicon-o-language">
                {{ __('messages.t_ap_update_language_files') }}
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
