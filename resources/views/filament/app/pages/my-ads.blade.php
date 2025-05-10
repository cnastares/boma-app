<x-filament-panels::page>
    <style>
        /* In CSS */
        :root {
            --icon-bg-color: none;
        }

        .fi-ta-empty-state-icon-ctn {
            background-color: var(--icon-bg-color);
        }

        /* Hide element in dark mode */
        .dark .fi-ta-empty-state-icon-ctn {
            background-color: var(--icon-bg-color);
        }

        .fi-ta-header {
            border-bottom: 1px solid var(--thumb-border-color, #fff);
        }

        .dark {
            --thumb-border-color: #18181B;
        }

        .classic {
            --thumb-border-color: #000;
        }

        /* @media (min-width: 768px) {
            .fi-ta-content::-webkit-scrollbar {
                display: none;
                WebKit
            }
        } */
    </style>
    {{ $this->table }}

</x-filament-panels::page>
