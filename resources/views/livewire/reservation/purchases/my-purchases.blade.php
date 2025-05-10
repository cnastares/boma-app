<div>
    <style>
        /* In CSS */
        :root {
            --icon-bg-color: none;
        }

        .fi-ta-empty-state-icon-ctn {
            background-color: var(--icon-bg-color);
        }

        .fi-ta-empty-state-icon-ctn>img {
            width: 10rem;
            height: 10rem;
        }

        /* Hide element in dark mode */
        .dark .fi-ta-empty-state-icon-ctn {
            background-color: var(--icon-bg-color);
        }
    </style>
    <livewire:layout.header isMobileHidden lazy />

    <x-page-header title="{{ __('messages.t_my_purchases') }}" isMobileHidden :$referrer />

    <x-user-navigation />

    <div class="container mx-auto px-4 py-10">
        @if ($isDisplayTabs && count($tabs) > 0)
        <x-filament::tabs class="mb-5">
            @foreach ($tabs as $tab)
            <x-filament::tabs.item :active="$activeTab === $tab" wire:click="setActiveTab('{{$tab}}')">
                {{ ucwords(str_replace('_', ' ', $tab)) }}
            </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>
        @endif

        {{ $this->table }}
    </div>
</div>
