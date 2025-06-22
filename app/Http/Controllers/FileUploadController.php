<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        if (! $file->isValid()) {
            throw new \RuntimeException('Uploaded file is not valid');
        }

        $filename = $file->getClientOriginalName() ?: Str::random(10) . '.jpg';

        if ($filename === '') {
            throw new \RuntimeException('Filename for upload is empty');
        }

        return [
            'path' => $file->storeAs('uploads', $filename, 'media'),
        ];
    }
}
