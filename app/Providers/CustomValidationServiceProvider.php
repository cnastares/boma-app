<?php

namespace App\Providers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Support\Facades\Lang;

class CustomValidationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    public function boot(ValidationFactory $validator)
    {

        // Load the custom messages
        $validator->resolver(function ($translator, $data, $rules, $messages, $customAttributes) {
            $messages = array_merge($messages, Lang::get('messages'));
            $globalCustomAttributes=[
                'email'=>__('messages.t_email'),
                'phone_number'=>__('messages.t_phone_number'),
                't_name'=>__('messages.t_name'),
                't_title'=>__('messages.t_title'),
                't_description'=>__('messages.t_description'),
                'password'=>__('messages.t_password'),
                'letters'=>__('messages.t_letters'),
                'mixed'=>__('messages.t_mixed'),
                'numbers'=>__('messages.t_numbers'),
                'symbols'=>__('messages.t_symbols'),
                'uncompromised'=>__('messages.t_uncompromised'),
            ];
            return new \Illuminate\Validation\Validator($translator, $data, $rules, $messages, [...$customAttributes,...$globalCustomAttributes]);
        });
    }
}
