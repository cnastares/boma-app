<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
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
    use LogsActivity;
    public function loginSocial(Request $request, string $provider): RedirectResponse
    {
        return $this->executeWithLogging($request, 'loginSocial', function ($traceId) use ($request, $provider) {
            $this->validateProvider($request);

            $this->logSecurityEvent($traceId, 'social_login_initiation', $request, [
                'provider' => $provider,
                'redirect_url' => $request->get('redirect', 'none')
            ]);

            return Socialite::driver($provider)->redirect();
        });
    }

    public function callbackSocial(Request $request, string $provider)
    {
        return $this->executeWithLogging($request, 'callbackSocial', function ($traceId) use ($request, $provider) {
            $this->validateProvider($request);

            $this->logSecurityEvent($traceId, 'social_callback_received', $request, [
                'provider' => $provider,
                'callback_params' => $request->query()
            ]);

            try {
                $response = Socialite::driver($provider)->user();

                $this->logSecurityEvent($traceId, 'social_user_data_retrieved', $request, [
                    'provider' => $provider,
                    'has_email' => !empty($response->getEmail()),
                    'has_name' => !empty($response->getName()),
                    'provider_user_id' => $response->getId()
                ]);

                // If email is missing, redirect to register page
                if (!$response->getEmail()) {
                    $this->logSecurityEvent($traceId, 'social_login_failed_missing_email', $request, [
                        'provider' => $provider,
                        'provider_user_id' => $response->getId(),
                        'user_name' => $response->getName()
                    ]);

                    return redirect()
                        ->route('register')
                        ->withInput([
                            'name' => $response->getName(),
                            'id' => $response->getId(),
                        ])
                        ->withErrors(['message' => __('messages.t_email_missing_from_facebook')]);
                }

                $email = $response->getEmail();

                $this->logSecurityEvent($traceId, 'social_email_extracted', $request, [
                    'provider' => $provider,
                    'email_domain' => substr(strrchr($email, "@"), 1)
                ]);

                // Check if the user exists, including soft-deleted records
                $existingUser = User::withTrashed()->where('email', $email)->first();

                if ($existingUser) {
                    return $this->handleExistingUser($traceId, $request, $provider, $existingUser, $response);
                } else {
                    return $this->createNewUser($traceId, $request, $provider, $email, $response);
                }

            } catch (\Exception $e) {
                $this->logSecurityEvent($traceId, 'social_callback_exception', $request, [
                    'provider' => $provider,
                    'error' => $e->getMessage(),
                    'error_type' => get_class($e)
                ]);
                throw $e;
            }
        });
    }

    private function handleExistingUser($traceId, Request $request, string $provider, User $existingUser, $response)
    {
        $this->logSecurityEvent($traceId, 'existing_user_found', $request, [
            'provider' => $provider,
            'user_id' => $existingUser->id,
            'is_soft_deleted' => $existingUser->trashed(),
            'email_verified' => !is_null($existingUser->email_verified_at)
        ]);

        // Check if the user was soft deleted
        if ($existingUser->trashed()) {
            $this->logSecurityEvent($traceId, 'social_login_blocked_soft_deleted', $request, [
                'provider' => $provider,
                'user_id' => $existingUser->id,
                'deleted_at' => $existingUser->deleted_at
            ]);

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __('messages.t_email_soft_deleted_contact_support')
                ]);
        }

        // Check if the email is linked to another provider
        $providerColumn = $provider . '_id';

        if (!$existingUser->$providerColumn) {
            $this->logSecurityEvent($traceId, 'social_login_blocked_different_provider', $request, [
                'provider' => $provider,
                'user_id' => $existingUser->id,
                'existing_providers' => $this->getUserProviders($existingUser)
            ]);

            // If the user exists but was registered using another method (manual/email/password)
            throw ValidationException::withMessages([
                'email' => __('messages.t_email_already_registered_different_provider')
            ]);
        }

        // Update the existing user with the new provider ID
        $existingUser->update([$providerColumn => $response->getId()]);
        
        $this->logSecurityEvent($traceId, 'social_login_successful_existing_user', $request, [
            'provider' => $provider,
            'user_id' => $existingUser->id,
            'provider_user_id' => $response->getId()
        ]);

        Auth::login($existingUser, remember: true);
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    private function createNewUser($traceId, Request $request, string $provider, string $email, $response)
    {
        $this->logSecurityEvent($traceId, 'creating_new_social_user', $request, [
            'provider' => $provider,
            'email_domain' => substr(strrchr($email, "@"), 1),
            'has_name' => !empty($response->getName())
        ]);

        // Create a new user if email is unique
        $user = User::create([
            'email' => $email,
            'password' => Str::random(10), // Generate a random password
            'name' => $response->getName() ?? explode('@', $email)[0],
            'email_verified_at' => now(),
            $provider . '_id' => $response->getId(),
        ]);

        $this->logSecurityEvent($traceId, 'social_user_created_successfully', $request, [
            'provider' => $provider,
            'user_id' => $user->id,
            'email_domain' => substr(strrchr($email, "@"), 1)
        ]);

        event(new Registered($user));

        Auth::login($user, remember: true);

        $this->logSecurityEvent($traceId, 'social_login_successful_new_user', $request, [
            'provider' => $provider,
            'user_id' => $user->id
        ]);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    private function getUserProviders(User $user): array
    {
        $providers = [];
        if ($user->facebook_id) $providers[] = 'facebook';
        if ($user->google_id) $providers[] = 'google';
        if ($user->password) $providers[] = 'email_password';
        return $providers;
    }

    protected function validateProvider(Request $request): array
    {
        return $this->getValidationFactory()->make(
            $request->route()->parameters(),
            ['provider' => 'in:facebook,google']
        )->validate();
    }
}
