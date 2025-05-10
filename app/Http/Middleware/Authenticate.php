<?php

namespace App\Http\Middleware;
use Closure;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class Authenticate extends Middleware
{
    /**
     * Attempt to authenticate using an API token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function authenticateToken(Request $request): bool
    {
        // First, try to get the token from headers
        $token = $request->header('token');

        // If no token in headers, try to get it from query parameters
        if (!$token) {
            $token = $request->query('token');
        }


        if (!$token) {
            return false;
        }

        $tokenModel = PersonalAccessToken::findToken($token);
        if (!$tokenModel) {
            return false;
        }

        $user = $tokenModel->tokenable;
        
        auth()->login($user);

        return true;
    }


    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request): ?string
    {
        if($this->authenticateToken($request)) {
            return $request->fullUrl();
        } else {
           return $request->expectsJson() ? null : route('login');
        }
    }
}
