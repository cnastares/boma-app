<x-filament-panels::page>
    <form action="" wire:submit='submit'>
        {{ $this->form }}
        <button type="submit" {{-- @disabled(!$canSubmit) --}}
            class="relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-primary fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-black text-white dark:text-black focus-visible:ring-primary-500/50 dark:bg-primary-600  dark:focus-visible:ring-primary-400/50  disabled:!bg-gray-400 mt-4 disabled:cursor-not-allowed">
            {{ __('messages.t_save') }}
        </button>
    </form>
</x-filament-panels::page>
