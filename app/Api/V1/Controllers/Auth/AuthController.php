<?php

namespace App\Api\V1\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthController
 * Handles authentication requests for the Adfox application.
 */
class AuthController extends Controller
{
    use LogsActivity;
    /**
     * Handle a login request to the application.
     *
     * This endpoint is documented at:
     * [Your API Documentation URL]#login
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        return $this->executeWithLogging($request, 'login', function ($traceId) use ($request) {
            $this->logApiRequest($traceId, $request, null, [
                'endpoint_type' => 'authentication',
                'email_provided' => !empty($request->email)
            ]);

            try {
                $request->validate([
                    'email' => 'required|email',
                    'password' => 'required',
                ]);

                $this->logSecurityEvent($traceId, 'api_login_attempt', $request, [
                    'email_domain' => $request->email ? substr(strrchr($request->email, "@"), 1) : null
                ]);

                $user = User::where('email', $request->email)->first();

                if (!$user) {
                    $this->logSecurityEvent($traceId, 'api_login_failed_user_not_found', $request, [
                        'email_domain' => substr(strrchr($request->email, "@"), 1),
                        'attempted_email' => $request->email
                    ]);

                    $response = response()->json(['message' => 'Invalid credentials'], 401);
                    $this->logApiRequest($traceId, $request, $response, [
                        'failure_reason' => 'user_not_found'
                    ]);
                    return $response;
                }

                if (!Hash::check($request->password, $user->password)) {
                    $this->logSecurityEvent($traceId, 'api_login_failed_invalid_password', $request, [
                        'user_id' => $user->id,
                        'user_active' => $user->email_verified_at !== null
                    ]);

                    $response = response()->json(['message' => 'Invalid credentials'], 401);
                    $this->logApiRequest($traceId, $request, $response, [
                        'failure_reason' => 'invalid_password',
                        'user_id' => $user->id
                    ]);
                    return $response;
                }

                $this->logSecurityEvent($traceId, 'api_login_successful', $request, [
                    'user_id' => $user->id,
                    'email_verified' => $user->email_verified_at !== null,
                    'last_login' => $user->updated_at
                ]);

                $token = $user->createToken('API Token')->plainTextToken;

                $response = response()->json([
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => $user
                ], 200);

                $this->logApiRequest($traceId, $request, $response, [
                    'success' => true,
                    'user_id' => $user->id,
                    'token_created' => true
                ]);

                return $response;

            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->logSecurityEvent($traceId, 'api_login_validation_failed', $request, [
                    'validation_errors' => array_keys($e->errors())
                ]);

                $response = response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);

                $this->logApiRequest($traceId, $request, $response, [
                    'failure_reason' => 'validation_error'
                ]);

                return $response;

            } catch (\Exception $e) {
                $this->logSecurityEvent($traceId, 'api_login_unexpected_error', $request, [
                    'error' => $e->getMessage(),
                    'error_type' => get_class($e)
                ]);

                $response = response()->json([
                    'message' => 'An unexpected error occurred'
                ], 500);

                $this->logApiRequest($traceId, $request, $response, [
                    'failure_reason' => 'server_error'
                ]);

                return $response;
            }
        });
    }

}
