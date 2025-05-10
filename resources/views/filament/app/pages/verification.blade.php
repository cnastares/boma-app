<x-filament-panels::page>
    <div>


        <div class="">
            @if (isset($this->record) && $this->record->status != 'declined')

                <div
                    class="rounded-xl md:bg-white md:shadow-sm  md:ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  classic:ring-black p-3">
                    <form wire:submit="{{ $this->getCurrentAction() }}" novalidate>
                        <div class="">
                            <div>
                                {{ $this->form }}
                            </div>
                        </div>
                        @if (!isset($this->record) || $this->record->status == 'declined')
                            <div
                                class="px-6 py-4 bg-white rounded-b-xl fixed md:static bottom-0 left-0 right-0 text-right border-t border-gray-200 classic:border-black dark:border-white/10 dark:bg-gray-900">
                                <x-button.secondary type="submit" size="lg" class="w-full md:w-auto min-w-[10rem]">
                                    {{ isset($this->record) && $this->record->status == 'declined' ? __('messages.t_reupload_docs') : __('messages.t_verify') }}
                                </x-button.secondary>
                            </div>
                        @endif
                    </form>
                    <x-filament-actions::modals />
                </div>
            @else
                <form wire:submit="{{ $this->getCurrentAction() }}" novalidate>
                    {{ $this->form }}
                    <button
                        type="submit"
                        @disabled(!$canSubmit)
                        class=" relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-primary fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-black text-white dark:text-black focus-visible:ring-primary-500/50 dark:bg-primary-600  dark:focus-visible:ring-primary-400/50  disabled:!bg-gray-400 mt-4 disabled:cursor-not-allowed">
                        {{ __('messages.t_submit_verify') }}
                         </button>
                </form>
            @endif
        </div>
    </div>

</x-filament-panels::page>
