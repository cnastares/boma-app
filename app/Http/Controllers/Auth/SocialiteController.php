<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

class SocialiteController extends Controller
{
    public function loginSocial(Request $request, string $provider): RedirectResponse
    {
        $this->validateProvider($request);

        return Socialite::driver($provider)->redirect();
    }

    public function callbackSocial(Request $request, string $provider)
    {
        $this->validateProvider($request);

        $response = Socialite::driver($provider)->user();

        // If email is missing, redirect to register page
        if (!$response->getEmail()) {
            return redirect()
                ->route('register')
                ->withInput([
                    'name' => $response->getName(),
                    'id' => $response->getId(),
                ])
                ->withErrors(['message' => __('messages.t_email_missing_from_facebook')]);
        }

        $email = $response->getEmail();

        // Check if the user exists, including soft-deleted records
        $existingUser = User::withTrashed()->where('email', $email)->first();

        if ($existingUser) {
            // Check if the user was soft deleted
            if ($existingUser->trashed()) {
                return redirect()
                    ->route('login')
                    ->withErrors([
                        'email' => __('messages.t_email_soft_deleted_contact_support')
                    ]);
            }

            // Check if the email is linked to another provider
            $providerColumn = $provider . '_id';

            if (!$existingUser->$providerColumn) {
                // If the user exists but was registered using another method (manual/email/password)
                throw ValidationException::withMessages([
                    'email' => __('messages.t_email_already_registered_different_provider')
                ]);
            }

            // Update the existing user with the new provider ID
            $existingUser->update([$providerColumn => $response->getId()]);
            Auth::login($existingUser, remember: true);
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Create a new user if email is unique
        $user = User::create([
            'email' => $email,
            'password' => Str::random(10), // Generate a random password
            'name' => $response->getName() ?? explode('@', $email)[0],
            'email_verified_at' => now(),
            $provider . '_id' => $response->getId(),
        ]);

        event(new Registered($user));

        Auth::login($user, remember: true);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    protected function validateProvider(Request $request): array
    {
        return $this->getValidationFactory()->make(
            $request->route()->parameters(),
            ['provider' => 'in:facebook,google']
        )->validate();
    }
}
