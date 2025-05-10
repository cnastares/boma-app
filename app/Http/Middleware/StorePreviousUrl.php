<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorePreviousUrl
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Store the previous URL in session if the user is NOT logged in
        if (!Auth::check() && !$request->is('login', 'register')) {
            session(['previous_url' => url()->previous()]);
        }

        return $next($request);
    }
}
