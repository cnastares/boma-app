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
            // Accessing the disk without writing a file ensures we don't leave test
            // artifacts behind while still confirming availability.
            Storage::disk('media')->files('/');

            Log::info('UploadImage: media disk accessible');
        } catch (\Exception $e) {
            Log::error('UploadImage: media disk not accessible', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function upload()
    {
        Log::info('UploadImage: upload initiated');

        $validated = $this->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,gif,webp,avif,bmp|max:10240',
        ]);

        Log::info('UploadImage: validation passed');

        // Check for upload errors before proceeding
        if (!isset($_FILES['photo']) || ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $errorCode = $_FILES['photo']['error'] ?? 'unknown';
            Log::error('UploadImage: upload error', ['error' => $errorCode]);
            $this->addError('photo', 'File upload failed. Please try again.');
            return;
        }

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
