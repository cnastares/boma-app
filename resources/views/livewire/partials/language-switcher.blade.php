<div class="relative mr-4">
    <x-filament::dropdown placement="bottom-end" >
            <x-slot name="trigger" >
                    <button type="button" aria-label="{{__('messages.t_aria_label_language_switcher')}}">
                    <div class="flex items-center gap-x-1" tabindex="0">
                        <div class="flex items-center gap-x-2">
                        @if(!is_null($icon))
                        <img src="{{$icon}}" alt="" class="w-7 rounded-sm h-5">
                        @endif
                        {{ $default_title }}
                    </div>
                        <x-icon-arrow-down-3 class="w-4 h-4 dark:text-gray-500" />
                    </div>
                    </button>
            </x-slot>
            <x-filament::dropdown.list role="menu">
                @foreach(fetch_active_languages() as $lang)
                <x-filament::dropdown.list.item role="menuitem" tabindex="0" wire:key='header-switcher-{{ $lang->lang_code  }}' wire:click="updateLocale('{{ $lang->lang_code }}')" wire:key="switch-{{ $lang->lang_code }}" @click="$refs.panel.close(event); ">
                    <div class="flex justify-between" >
                        <div class="flex items-center gap-x-2">
                            @if ($lang->icon)
                            <img src="{{ \Storage::url($lang->icon) }}" alt="" class="w-7 rounded-sm h-5">
                            @endif
                            {{ $lang->title }}
                        </div>
                        @if($default_lang_code === $lang->lang_code)
                            <x-heroicon-o-check class="w-4 h-4 dark:text-gray-500" />
                        @endif
                    </div>
                </x-filament::dropdown.list.item>
                @endforeach
            </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>


