<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class FileUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Log all request data for debugging
        Log::info('[FileUpload] Request data:', [
            'files' => $request->allFiles(),
            'has_file' => $request->hasFile('file'),
            'has_files' => $request->hasFile('files'),
            'all_input' => $request->all()
        ]);

        // Check for different possible file field names
        $file = null;
        $fileFieldName = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileFieldName = 'file';
        } elseif ($request->hasFile('files')) {
            $file = $request->file('files');
            $fileFieldName = 'files';
        } elseif ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $fileFieldName = 'upload';
        } else {
            // Try to get the first file from the request
            $files = $request->allFiles();
            Log::info('[FileUpload] All files structure:', [
                'files' => $files,
                'files_type' => gettype($files),
                'files_keys' => array_keys($files)
            ]);

            if (!empty($files)) {
                $fileFieldName = array_key_first($files);
                $file = $files[$fileFieldName];

                Log::info('[FileUpload] File from allFiles:', [
                    'field_name' => $fileFieldName,
                    'file' => $file,
                    'file_type' => gettype($file),
                    'is_array' => is_array($file),
                    'array_count' => is_array($file) ? count($file) : 'not_array'
                ]);

                // Handle case where $file might be an array of files
                if (is_array($file)) {
                    // If it's an array, get the first element
                    $file = $file[0] ?? null;
                    Log::info('[FileUpload] Extracted from array:', [
                        'extracted_file' => $file,
                        'extracted_type' => gettype($file),
                        'is_object' => is_object($file)
                    ]);
                }
            }
        }

        // Handle case where $request->file() returns an array
        if (is_array($file)) {
            Log::info('[FileUpload] File is array, extracting first element');
            $file = $file[0] ?? null;
        }

        if (!$file) {
            Log::error('[FileUpload] No file found in request');
            return response()->json([
                'errors' => [
                    'file' => ['No file uploaded']
                ]
            ], 400);
        }

        // Ensure $file is a valid file object
        if (!is_object($file) || !method_exists($file, 'getClientOriginalName')) {
            Log::error('[FileUpload] Invalid file object received', [
                'file_type' => gettype($file),
                'file_class' => is_object($file) ? get_class($file) : 'not_object',
                'file_value' => $file
            ]);
            return response()->json([
                'errors' => [
                    'file' => ['Invalid file object']
                ]
            ], 422);
        }

        Log::info('[FileUpload] File found:', [
            'field_name' => $fileFieldName,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'valid' => $file->isValid(),
        ]);

        if (!$file->isValid() || !$file->getClientOriginalName()) {
            Log::error('Upload fallido: archivo inválido o vacío');
            return response()->json([
                'errors' => [
                    'file' => ['Archivo inválido o vacío']
                ]
            ], 422);
        }

        try {
            $path = $file->storePublicly('livewire-tmp', 'media');
            Log::info('Upload exitoso', ['path' => $path]);

            return response()->json([
                'path' => $path,
            ]);
        } catch (\Exception $e) {
            Log::error('[FileUpload] Storage error', ['error' => $e->getMessage()]);
            return response()->json([
                'errors' => [
                    'file' => ['Error storing file: ' . $e->getMessage()]
                ]
            ], 500);
        }
    }
}
