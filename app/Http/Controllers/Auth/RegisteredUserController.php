<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
        $loginOtpSettings = app(LoginOtpSettings::class);

        $authSettings = app(AuthSettings::class);

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
            // Manipulate the Validation attributes labels
            $attributes = \Arr::add($attributes, $field['id'], $field['name']);
        }
        // dd($validationRules);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['unique:users,phone_number'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => [new GoogleReCaptchaV3ValidationRule('register')],
            ...$validationRules
        ], $customMessages, $attributes);
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




        $user = User::create($data);

        event(new Registered($user));

        Auth::login($user);

        if ($loginOtpSettings->enabled) {
            $sendOtp = new SendOtp;
            //call send otp method
            return $sendOtp->sendOtp($user->phone_number);
        } else {
            Auth::login($user);
            return redirect(RouteServiceProvider::HOME);
        }
    }
}

