<div class="flex items-center gap-4 relative theme-switch {{ $appearanceSettings->enable_contrast_toggle ? 'flex items-center gap-8' : '' }}"
    x-data="{
        theme: $persist('light'),
        init() {
            this.$nextTick(() => {
                this.updateTheme();
            });
        },
        updateTheme() {
            const drakcontainer = document.querySelector('.fi-dropdown-trigger');
            if (this.theme === 'classic') {
                document.documentElement.classList.add('classic');
                document.documentElement.classList.remove('dark');
                drakcontainer.classList.remove('text-primary-600');


            } else if (this.theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('classic');
                drakcontainer.classList.add('text-primary-600');


            } else {
                document.documentElement.classList.remove('classic');
                document.documentElement.classList.remove('dark');
                drakcontainer.classList.remove('text-primary-600');
            }
        }


    }" x-init="$watch('theme', () => {
        $dispatch('theme-changed', theme);
        updateTheme();
    })">
    <style>
        .notification svg {
            width: 1.675rem;
            height: 1.675rem;
        }

        .dark {
            --primary: {{ $appearanceSettings?->primary_color ?? 'rgba(253, 174, 75, 1)' }};
        }

        .post-ad {
            background-color: var(--primary, #000);
        }

        .classic .fi-dropdown-panel .fi-dropdown-header + .fi-dropdown-list + .fi-dropdown-list > a {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            border-bottom: 1px solid black;
        }
    </style>
    @if ($appearanceSettings->enable_theme_switcher)
    <div class="h-8 w-[30px] flex items-center relative">
            <x-filament::dropdown placement="bottom-end" x-cloak teleport x-data x-tooltip="{
                content: '{{__('messages.t_tooltip_theme_switcher')}}',
                theme: $store.theme,
            }">
                <x-slot name="trigger">
                    <button type="button" aria-label="{{__('messages.t_aria_label_theme_switcher')}}"
                        class="flex h-7 w-7  items-center justify-center rounded-full ring-[0.1rem] ring-black dark:bg-black dark:ring-inset dark:ring-white/5">
                        <x-icon-light x-show="theme === 'light'" class="w-5 h-5 dark:text-gray-500  " aria-hidden="true" />
                        <x-icon-dark x-show="theme === 'dark'" class="w-6 h-6 dark:text-primary-600 " aria-hidden="true" />
                        <x-heroicon-o-square-3-stack-3d x-show="theme === 'classic'"
                            class="w-6 h-6 dark:text-primary-600" aria-hidden="true" />
                    </button>
                </x-slot>

                <x-filament::dropdown.list role="menu">
                    <x-filament::dropdown.list.item role="menu-item" icon="light" x-on:click="(theme = 'light') &amp;&amp; close()">
                        {{ __('messages.t_light_mode') }}
                    </x-filament::dropdown.list.item>
                    <x-filament::dropdown.list.item role="menu-item" icon="dark" class="dark:text-primary-600"
                        x-on:click="(theme = 'dark') &amp;&amp; close()">
                        {{ __('messages.t_dark_mode') }}
                    </x-filament::dropdown.list.item>
                    <x-filament::dropdown.list.item role="menu-item" icon="heroicon-o-square-3-stack-3d"
                        x-on:click="(theme = 'classic') &amp;&amp; close();">
                        {{ __('messages.t_classic_mode') }}
                    </x-filament::dropdown.list.item>
                </x-filament::dropdown.list>
            </x-filament::dropdown>
        </div>
        @endif
    @if ($appearanceSettings->enable_contrast_toggle)
        <div style="margin-right: -25px;" x-data x-tooltip="{
            content: '{{__('messages.t_tooltip_theme_switcher')}}',
            theme: $store.theme,
        }" class='pt-1'>
            <x-contrast-toggle key="toggle-2" />
        </div>
    @endif
    <div class=" notification" tabindex="0" @keydown.enter="$dispatch('open-modal', { id: 'database-notifications' })" x-tooltip="{
        content: '{{__('messages.t_tooltip_notifications')}}',
        theme: $store.theme,
    }">
        @livewire('database-notifications')
    </div>
    {{-- <x-icon-arrow-down-3 class="w-4 h-4 dark:text-gray-500" /> --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            //Add main-content id for skip link
            const main = document.getElementsByClassName('fi-main');
            if (main.length > 0) {
                main[0].id = "main-content";
            }
            //Add sidebar-nav id for skip link
            const sidebar = document.getElementsByClassName('fi-sidebar-nav');
            if (sidebar.length > 0) {
                sidebar[0].id = "sidebar-nav";
            }
            //Add fi-dropdown-trigger id for skip link
            const dropdownTrigger = document.getElementsByClassName('fi-dropdown-trigger');
            if (dropdownTrigger.length > 0) {
                dropdownTrigger[0].tabindex = "0";
            }
        });
        const intervalId = setInterval(() => {
            const iconContainer = document.querySelector('.fi-dropdown-header-icon');
            const listContainer = document.querySelector('.fi-dropdown-list-item-icon');
            // Define the new SVG content for the header icon
            const newSVG = `
    <svg class=" h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" height="24" width="24">
<g id="user-protection-2âshield-secure-security-profile-person"><path id="Rectangle 38" stroke="currentColor" d="m2.5 2 0 15 9.5 5 9.5-5 0-15-19 0Z"></path><path id="Ellipse 350" stroke="currentColor" d="M9 8a3 3 0 1 0 6 0 3 3 0 1 0-6 0"></path><path id="Ellipse 417" stroke="currentColor" d="M18 19v-3.653A13.945 13.945 0 0 0 12 14c-2.147 0-4.181.483-6 1.347V19"></path></g>
</svg>
    `;
            // Replace content for fi-dropdown-header-icon if it exists
            if (iconContainer) {
                iconContainer.outerHTML = newSVG;
            }

            // Clear the interval if both elements are found and updated
            if (iconContainer) {
                clearInterval(intervalId);
            }
        }, 500); // Runs every 100 milliseconds until both icons are replaced

        // Dashboard header page
        const imageUrl = setInterval(() => {
            let fiAvatar = document.querySelector(".fi-avatar");
            // Create a new <p> element
            const newParagraph = document.createElement("span");

            // Select the parent element of the 'para' class
            const parentElement = fiAvatar.parentElement;

            // Add the 'main' class to the parent element
            parentElement.classList.add("main");

            // Apply flex styling to the 'main' class
            parentElement.style.display = "flex";
            parentElement.style.alignItems = "center";
            parentElement.style.columnGap = "4px";

            // Set text content for the <p> element
            newParagraph.innerHTML =
                `<x-icon-arrow-down-3 class='w-4 h-4 rtl:ml-1 dark:text-gray-500'></x-icon-arrow-down-3>`;

            // Insert the new <p> after the existing one
            fiAvatar.insertAdjacentElement("afterend", newParagraph);

            if (fiAvatar) {
                let url = fiAvatar.getAttribute("src");
                let textColor = "000000";
                let backgroundColor = "D9D9D9";
                let updatedUrl = url
                    .replace("FFFFFF", textColor)
                    .replace("09090b", backgroundColor);

                if (url !== updatedUrl) {
                    fiAvatar.setAttribute("src", updatedUrl);
                }

                clearInterval(imageUrl);
            } else {
                dark
                console.error('Image Url not update');
            }
        }, 100);
    </script>
</div>
