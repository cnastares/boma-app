<div>
    <form wire:submit.prevent="upload" class="space-y-4">
        <input type="file" wire:model="photo" accept="image/*" class="border p-2">
        @error('photo') <span class="text-red-600">{{ $message }}</span> @enderror

        <div x-data="{ progress: 0 }"
             @livewire-upload-progress="progress = $event.detail.progress"
             @livewire-upload-finish="progress = 100">
            <div x-show="progress > 0" class="w-full bg-gray-200 rounded">
                <div class="bg-blue-600 text-xs leading-none py-1 text-center text-white rounded" :style="`width: ${progress}%`">
                    <span x-text="progress + '%'">0%</span>
                </div>
            </div>
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Upload</button>
    </form>

    @if ($savedImage)
        <div class="mt-4">
            <img src="{{ $savedImage }}" alt="Uploaded image" class="max-w-xs">
        </div>
    @endif
</div>
