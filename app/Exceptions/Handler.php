<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportException;

use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TransportException) {
            // Log the error for debugging purposes
            Log::error('Custom SMTP Error: ' . $exception->getMessage());

            // Redirect back with a user-friendly error message
            return back()->withErrors(['email' => 'Your account has been created, but Email verification failed due to a configuration issue. Please notify the site administrator to check and update the email settings in the admin panel.']);
        }

        return parent::render($request, $exception);
    }
}
