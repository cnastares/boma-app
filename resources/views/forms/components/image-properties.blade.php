<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500 fi-fo-key-value">
        <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }" class="w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
            <div class="flex gap-2 items-center  justify-between w-full">
                <div class="w-full  px-3 py-2 text-start text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('messages.t_image_order') }}
                </div>
                <div class="w-full  px-3 py-2 text-start text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('messages.t_alt_text') }}
                </div>
            </div>
            @isset($this->data['image_properties'])
            @foreach ($this->data['image_properties'] as $key => $item)
                <div class="flex gap-2 items-center  justify-between w-full">
                    <div
                        class="w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0 ps-3 pe-3 font-mono">
                        {{ $key }}
                    </div>
                    <div class="w-full">
                        <x-filament::input type="text" value="{{ $item }}"
                            wire:model.blur='data.image_properties.{{ $key }}' />
                    </div>
                    <div>
                        <x-heroicon-o-trash  @click="console.log($wire);$wire.dispatchFormEvent('remove-image-property', '{{ $key }}')" class="h-5 w-5 cursor-pointer text-red-600" />
                    </div>
                </div>
            @endforeach
            @endisset
        </div>
    </div>


</x-dynamic-component>
