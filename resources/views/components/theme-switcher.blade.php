<x-filament::dropdown placement="top-end" x-cloak >
    <x-slot name="trigger">
        <button type="button" x-data x-tooltip="{
            content: '{{__('messages.t_tooltip_theme_switcher')}}',
            theme: $store.theme,
        }"
            aria-label="{{ __('messages.t_tooltip_theme_switcher') }}"
            class="flex h-7 w-7 mr-4 items-center justify-center rounded-full ring-[0.1rem] ring-black dark:bg-black dark:ring-inset dark:ring-white/5">
            <x-icon-light x-show="theme === 'light'" class="w-5 h-5 dark:text-gray-500" aria-hidden="true" />
            <x-icon-dark x-show="theme === 'dark'" class="w-6 h-6 dark:text-primary-600" aria-hidden="true" />
            <x-heroicon-o-square-3-stack-3d x-show="theme === 'classic'" class="w-6 h-6 dark:text-primary-600" aria-hidden="true" />
        </button>
    </x-slot>

    <x-filament::dropdown.list role="menu">
        <x-filament::dropdown.list.item role="menuitem" icon="light" @click="theme = 'light'; $refs.panel.close(event);">
            {{ __('messages.t_light_mode') }}
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item role="menuitem" icon="dark" @click="theme = 'dark'; $refs.panel.close(event);">
            {{ __('messages.t_dark_mode') }}
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item role="menuitem" icon="heroicon-o-square-3-stack-3d" @click="theme = 'classic'; $refs.panel.close(event);">
            {{ __('messages.t_classic_mode') }}
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>
