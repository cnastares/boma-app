<div x-data="{ showModal: false, url: '{{getAdPlaceholderImage($record->id)}}' }">
    @if ($adSettings->admin_approval_required && ($this->record && $this->record->status->value!='draft'))
    <p class="text-gray-600 mb-5 text-sm">{!! __('messages.t_image_admin_approval_instruction') !!}</p>
    @endif
    <form>

        {{ $this->form }}
    </form>
    <!-- Placeholder modal -->
    <div  x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center" x-cloak>
        <div class="relative mx-auto rounded-md">
            <div class="p-10 overflow-auto max-h-[90vh]" >
                <img :src="url" alt="" class="w-full  rounded-md" >
            </div>
            <!-- Close Button -->
            <button arai-label="{{__('messages.t_aria_label_close')}}" type="button" @click="showModal = false" class="fixed flex items-center justify-center top-0 right-0 mt-2 mr-2 w-10 h-10 text-white bg-black bg-opacity-50 rounded-full"
            x-tooltip="{
                content: '{{__('messages.t_tooltip_close')}}',
                theme: $store.theme,
            }">
                <x-heroicon-o-x-mark class="w-6 h-6" aria-hidden="true" />
            </button>
        </div>
    </div>
    <x-filament-actions::modals />

</div>
