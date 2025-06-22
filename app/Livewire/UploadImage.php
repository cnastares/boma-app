<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UploadImage extends Component
{
    use WithFileUploads;

    public $photo;
    public $savedImage;

    public function mount()
    {
        try {
            Storage::disk('media')->put('disk_test.txt', 'test');
            Storage::disk('media')->delete('disk_test.txt');
            Log::info('UploadImage: media disk is writable');
        } catch (\Exception $e) {
            Log::error('UploadImage: media disk not accessible', ['error' => $e->getMessage()]);
        }
    }

    public function upload()
    {
        Log::info('UploadImage: upload initiated');

        $validated = $this->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,gif,webp,avif,bmp|max:10240',
        ]);

        Log::info('UploadImage: validation passed');

        $path = 'uploads';
        if (empty($path)) {
            Log::error('UploadImage: storage path is empty');
            return;
        }

        $tmpPath = $this->photo->getRealPath();
        if (empty($tmpPath)) {
            Log::error('UploadImage: uploaded file path is empty');
            return;
        }

        try {
            $storedPath = $this->photo->store($path, 'media');
            Log::info('UploadImage: file stored', ['path' => $storedPath]);
            $this->savedImage = Storage::disk('media')->url($storedPath);
        } catch (\Exception $e) {
            Log::error('UploadImage: store failed', ['error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.upload-image');
    }
}
