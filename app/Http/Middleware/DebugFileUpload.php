<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugFileUpload
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            Log::debug('Upload intercepted', [
                'name' => $file->getClientOriginalName(),
                'tempPath' => $file->getPathname(),
                'targetDisk' => config('filesystems.default'),
            ]);
        }

        return $next($request);
    }
}
