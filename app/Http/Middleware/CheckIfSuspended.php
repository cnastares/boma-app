<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfSuspended
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip suspension check for admin routes
        if ($request->is('admin/*')) {
            return $next($request);
        }

        //Check if user is suspended.Then logout the user
        if(Auth::check() && (!Auth::user()->is_admin) && Auth::user()->suspended){
            Auth::logout();
            return redirect()->route('login')->withErrors(['account_suspended'=>__('messages.t_account_suspended')]);
        }
        
        return $next($request);
    }
}
