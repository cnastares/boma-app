<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CommonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if (file_exists(storage_path('installed'))) {
            return $next($request);
        } else {
            return redirect()->to('/install');
        }
    }
}
