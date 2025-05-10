<x-filament-panels::page>
    <div class="flex p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <span class="sr-only">Attention</span>
        <div>
            <span class="font-medium">Key Points to Keep in Mind:</span>
            <ul class="mt-1.5 ml-4 list-disc list-inside">
                <li>Avoid translating terms starting with `:` as these denote system variables. For instance, "Greeting :username" might be displayed as "Greeting John".</li>
                <li>Do not use characters like " , ' , \, and ` as they can lead to system malfunctions.</li>
                <li>Translations are automatically saved. Input your translation and click outside the box to save.</li>
            </ul>
        </div>
    </div>


    <div class="flex justify-between items-center">
        <div class="flex w-[300px]">
            <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass" class="w-full">
                <x-filament::input
                    type="text"
                    placeholder="Search translations entries... "
                    wire:model.live="q"
                />
            </x-filament::input.wrapper>
        </div>
    </div>


    <div class="fi-fo-key-value rounded-lg shadow-sm ring-1 transition duration-75 focus-within:ring-2 bg-white dark:bg-white/5 ring-gray-950/10 focus-within:ring-primary-600 dark:focus-within:ring-primary-500 dark:ring-white/20">
        <div class="divide-y divide-gray-200 dark:divide-white/10">
            <table class="w-full table-auto divide-y divide-gray-200 dark:divide-white/5 ">
                <thead>
                    <tr>
                        <th scope="col" class="px-3 py-2 text-start text-sm font-medium text-gray-700 dark:text-gray-200">
                            Key
                        </th>
                        <th scope="col" class="px-3 py-2 text-start text-sm font-medium text-gray-700 dark:text-gray-200">
                            Value
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->translation as $key => $value)
                        <tr class="divide-x divide-gray-200 rtl:divide-x-reverse dark:divide-white/5 border-t dark:border-white/5" wire:key="translation-{{ $key }}">
                            <td class="w-1/2 px-4 text-base sm:text-sm">
                                {{ $key }}
                            </td>
                            <td class="w-1/2 p-0 ">
                                <input  class="fi-input block w-full border-none bg-transparent py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 ps-3 pe-3 font-mono" type="text" wire:model.blur='data.{{ $key }}' />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
