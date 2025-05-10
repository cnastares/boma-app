<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
<div x-data="{
    state: $wire.entangle('{{ $getStatePath() }}'),
    counter: 0,
    snapshot() {
        if (this.counter > 5) {
            window.$wireui.notify({
                title      : '{{ __('messages.t_error') }}',
                description: '{{ __('messages.t_unable_to_take_more_selfies') }}',
                icon       : 'error'
            });
            return;
        }
        const _this = this;
        Webcam.snap(function(data_uri) {
            updateSnapshotData(data_uri);

        });
        this.counter += 1;
    }
}">


        <div>
            {{-- Container/Results --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4" wire:ignore.self>

                {{-- Camera container --}}
                <div class="flex items-center justify-center border-dashed border-2 border-gray-200 classic:border-black dark:border-zinc-700  mb-4">
                    <div id="webcamjs-container" wire:ignore></div>
                </div>

                {{-- Image taken --}}
                <div class="flex items-center justify-center border-dashed border-2 border-gray-200 classic:border-black dark:border-zinc-700 mb-4">
                    <div id="webcamjs-results" ></div>
                </div>

            </div>
            {{-- Take Snapshot --}}
            <div>
                <button type="button" class="text-white bg-primary-700 hover:bg-primary-500 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded text-sm px-5 py-2.5 text-center inline-flex items-center ltr:mr-2 rtl:ml-2 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800" x-on:click="snapshot">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ltr:mr-2 rtl:ml-2 ltr:-ml-1 rtl:-mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="text-xs font-semibold">{{ __('messages.t_take_a_selfie') }}</span>
                </button>
            </div>

            {{-- Initialize Js --}}
            <script>
                Webcam.set({
                    width       : 490,
                    height      : 350,
                    image_format: 'jpeg',
                    jpeg_quality: 100
                });
                Webcam.attach('#webcamjs-container');
                document.addEventListener('livewire:init', () => {
                Livewire.on('document-type-updated',function(){
                    Webcam.set({
                    width       : 490,
                    height      : 350,
                    image_format: 'jpeg',
                    jpeg_quality: 100
                });
                Webcam.attach('#webcamjs-container');
                })
                })
            </script>
        </div>

    </div>

</x-dynamic-component>
@push('styles')
    {{-- Include WebcamJS Plugin --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
@endpush

@push('scripts')
<script>
    function updateSnapshotData(dataUri) {
        document.getElementById('webcamjs-results').innerHTML = `<img src="${dataUri}" />`;
        @this.dispatch('take-selfie', { dataUri });
    }
</script>
@endpush
