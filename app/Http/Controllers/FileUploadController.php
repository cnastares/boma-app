<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function store(Request $request)
    {
        if (! $request->hasFile('file')) {
            abort(400, 'No file uploaded');
        }

        $file = $request->file('file');

        Log::info('[FileUpload]', [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'valid' => $file->isValid(),
        ]);

        if (! $file->isValid() || ! $file->getClientOriginalName()) {
            Log::error('Upload fallido: archivo inválido o vacío');
            abort(422, 'Archivo inválido o vacío');
        }

        $path = $file->storePublicly('livewire-tmp', 'media');
        Log::info('Upload exitoso', ['path' => $path]);

        return [
            'path' => $path,
        ];
    }
}
