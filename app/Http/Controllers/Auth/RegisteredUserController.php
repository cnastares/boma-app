<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use TimeHunter\LaravelGoogleReCaptchaV3\Validations\GoogleReCaptchaV3ValidationRule;
use Adfox\LoginOtp\Livewire\SendOtp;
use App\Rules\UniqueDynamicField;
use App\Settings\AuthSettings;
use App\Settings\LoginOtpSettings;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    use LogsActivity;
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        return $this->executeWithLogging($request, 'store', function ($traceId) use ($request) {
            $loginOtpSettings = app(LoginOtpSettings::class);
            $authSettings = app(AuthSettings::class);

            $this->logSecurityEvent($traceId, 'user_registration_attempt', $request, [
                'email_domain' => $request->email ? substr(strrchr($request->email, "@"), 1) : null,
                'has_phone' => !empty($request->phone),
                'otp_enabled' => $loginOtpSettings->enabled,
                'dynamic_fields_count' => count($authSettings->custom_registration_fields ?? [])
            ]);

            try {
                $customMessages = [];
                $attributes = [];
                $dynamicFields = $authSettings->custom_registration_fields ?? [];
                $validationRules = [];
                
                foreach ($dynamicFields as $field) {
                    $validationRules[$field['id']][] = $field['required'] ? 'required' : 'nullable';
                    if($field['type'] === 'number'){
                        $validationRules[$field['id']][]='numeric';
                        if(isset($field['max_digits'])){
                            $validationRules[$field['id']][]='max_digits:'.$field['max_digits'];
                        }
                        if(isset($field['min_digits'])){
                            $validationRules[$field['id']][]='min_digits:'.$field['min_digits'];
                        }
                    }
                    if(in_array($field['type'],['number','text']) && isset($field['is_unique']) && $field['is_unique']){
                        $validationRules[$field['id']][]=new UniqueDynamicField($field['id'],'users','dynamic_fields',$field['name']);
                    }
                    if ($field['type'] === 'email') {
                        $validationRules[$field['id']][] = 'email';
                    }
                    $attributes = \Arr::add($attributes, $field['id'], $field['name']);
                }

                $this->logSecurityEvent($traceId, 'registration_validation_start', $request, [
                    'validation_rules_count' => count($validationRules),
                    'dynamic_fields' => array_keys($validationRules)
                ]);

                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                    'phone' => ['unique:users,phone_number'],
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
                    'g-recaptcha-response' => [new GoogleReCaptchaV3ValidationRule('register')],
                    ...$validationRules
                ], $customMessages, $attributes);

                $this->logSecurityEvent($traceId, 'registration_validation_passed', $request, [
                    'email_domain' => substr(strrchr($request->email, "@"), 1)
                ]);

                $data = $loginOtpSettings->enabled ?
                    [
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone_number' => $request->phone,
                        'password' => Hash::make($request->password),
                        'facebook_id'=>$request->facebook_id
                    ] :
                    [
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'facebook_id'=>$request->facebook_id
                    ];

                // Prepare data for dynamic fields
                $dynamicData = [];
                foreach ($authSettings->custom_registration_fields as $field) {
                    $dynamicData[] = [
                        'id' => $field['id'],
                        'name' => $field['name'],
                        'value' => $request->input($field['id'])
                    ];
                }

                $data['dynamic_fields'] = $dynamicData;

                $this->logSecurityEvent($traceId, 'creating_user_account', $request, [
                    'email_domain' => substr(strrchr($request->email, "@"), 1),
                    'has_phone' => !empty($request->phone),
                    'dynamic_fields_populated' => count($dynamicData)
                ]);

                $user = User::create($data);

                $this->logSecurityEvent($traceId, 'user_account_created', $request, [
                    'user_id' => $user->id,
                    'email_domain' => substr(strrchr($user->email, "@"), 1)
                ]);

                event(new Registered($user));

                Auth::login($user);

                $this->logSecurityEvent($traceId, 'user_logged_in_after_registration', $request, [
                    'user_id' => $user->id,
                    'otp_flow' => $loginOtpSettings->enabled
                ]);

                if ($loginOtpSettings->enabled) {
                    $this->logSecurityEvent($traceId, 'otp_verification_initiated', $request, [
                        'user_id' => $user->id,
                        'phone_number_provided' => !empty($user->phone_number)
                    ]);

                    $sendOtp = new SendOtp;
                    return $sendOtp->sendOtp($user->phone_number);
                } else {
                    $this->logSecurityEvent($traceId, 'registration_completed_successfully', $request, [
                        'user_id' => $user->id
                    ]);

                    return redirect(RouteServiceProvider::HOME);
                }

            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->logSecurityEvent($traceId, 'registration_validation_failed', $request, [
                    'validation_errors' => array_keys($e->errors()),
                    'error_count' => count($e->errors())
                ]);
                throw $e;
            } catch (\Exception $e) {
                $this->logSecurityEvent($traceId, 'registration_unexpected_error', $request, [
                    'error' => $e->getMessage(),
                    'error_type' => get_class($e)
                ]);
                throw $e;
            }
        });
    }
}

