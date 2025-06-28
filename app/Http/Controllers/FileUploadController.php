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
                'file_value' => is_array($file) ? 'array_with_' . count($file) . '_elements' : $file
            ]);
            return response()->json([
                'errors' => [
                    'file' => ['Invalid file object - received ' . gettype($file)]
                ]
            ], 422);
        }

        // Additional validation for UploadedFile
        if (!($file instanceof \Illuminate\Http\UploadedFile)) {
            Log::error('[FileUpload] File is not an UploadedFile instance', [
                'actual_class' => get_class($file),
                'expected' => 'Illuminate\Http\UploadedFile'
            ]);
            return response()->json([
                'errors' => [
                    'file' => ['Invalid file type - not an UploadedFile instance']
                ]
            ], 422);
        }

        // Validate file path and temporary file
        if (!$file->getPathname() || !file_exists($file->getPathname())) {
            Log::error('[FileUpload] Invalid temporary file path', [
                'pathname' => $file->getPathname(),
                'exists' => $file->getPathname() ? file_exists($file->getPathname()) : 'no_pathname'
            ]);
            return response()->json([
                'errors' => [
                    'file' => ['Invalid temporary file - file not found on server']
                ]
            ], 422);
        }

        Log::info('[FileUpload] File found:', [
            'field_name' => $fileFieldName,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'valid' => $file->isValid(),
            'pathname' => $file->getPathname(),
            'temp_exists' => file_exists($file->getPathname())
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
            if (!$request->hasFile($fileFieldName)) {
                Log::error('No se recibió archivo en la solicitud');
                return response()->json([
                    'errors' => [
                        'file' => ['Archivo no proporcionado']
                    ]
                ], 400);
            }

            if (!$file->isValid()) {
                Log::error('Archivo inválido');
                return response()->json([
                    'errors' => [
                        'file' => ['Archivo inválido']
                    ]
                ], 422);
            }

            $filename = $file->getClientOriginalName();
            if (!$filename) {
                Log::error('Nombre de archivo no disponible');
                return response()->json([
                    'errors' => [
                        'file' => ['Nombre de archivo no disponible']
                    ]
                ], 422);
            }

            // Use the livewire-tmp directory directly without date subdirectories
            $directory = 'livewire-tmp';

            // Ensure the directory exists in the media disk
            $disk = Storage::disk('media');
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
                Log::info('[FileUpload] Created directory', ['directory' => $directory]);
            }

            // Use move instead of storeAs to avoid path issues
            $uniqueFilename = time() . '_' . uniqid() . '_' . $filename;
            $fullPath = $directory . '/' . $uniqueFilename;

            // Copy the file using the disk's put method
            $fileContents = file_get_contents($file->getPathname());
            if ($fileContents === false) {
                throw new \Exception('Could not read temporary file contents');
            }

            $stored = $disk->put($fullPath, $fileContents);

            if (!$stored) {
                throw new \Exception('Failed to store file on disk');
            }

            // Verify the file was actually stored
            if (!$disk->exists($fullPath)) {
                Log::error('[FileUpload] File storage failed - file not found after storage', [
                    'path' => $fullPath,
                    'directory' => $directory,
                    'filename' => $uniqueFilename
                ]);
                throw new \Exception('File storage failed - file not found after storage');
            }

            Log::info('Archivo subido correctamente', ['path' => $fullPath]);

            return response()->json([
                'paths' => [$fullPath]
            ]);
        } catch (\Exception $e) {
            Log::error('[FileUpload] Storage error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'errors' => [
                    'file' => ['Error storing file: ' . $e->getMessage()]
                ]
            ], 500);
        }
    }
}
