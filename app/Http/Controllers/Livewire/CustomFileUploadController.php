<?php

namespace App\Http\Controllers\Livewire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Overrides Livewire's default temporary file upload endpoint.
 *
 * Each uploaded file is validated using the rules defined in
 * `config('livewire.temporary_file_upload.rules')`. Files without a
 * real path are rejected and a 400 response is returned.
 */
class CustomFileUploadController extends Controller
{
    public function handle(Request $request)
    {
        $rules = config('livewire.temporary_file_upload.rules');

        $request->validate([
            'files.*' => $rules,
        ]);

        /** @var UploadedFile $file */
        foreach ($request->file('files', []) as $file) {
            if (empty($file->getRealPath())) {
                return response()->json(['message' => 'Invalid file'], 400);
            }

            $file->storeAs(
                config('livewire.temporary_file_upload.directory'),
                $file->hashName(),
                ['disk' => config('livewire.temporary_file_upload.disk')]
            );
        }

        return response()->json(['message' => 'Files uploaded']);
    }
}
