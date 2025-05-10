<x-auth-layout>
    {{-- @assets --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    {{-- @endassets --}}
    <!-- Header -->
    <header class="flex justify-between items-center mb-6">
        <x-brand />
        <a href="/" class="hover:underline text-lg flex items-center gap-x-2" >
            <span>
                <x-heroicon-o-arrow-left class="w-5 h-5 cursor-pointer" />
            </span>
            {{ __('messages.t_back_to_home') }}
        </a>
    </header>

    <main id="main-content">
    <!-- Register Title -->
    <h1 class="text-2xl md:text-3xl mt-12 mb-8 font-semibold">{{ __('messages.t_create_account_prompt', ['siteName' =>
        $generalSettings->site_name]) }}</h1>


    @if($authSettings->enable_google_login || $authSettings->enable_facebook_login || $loginOtpSettings->enabled)
    <x-social-links />
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="rounded-xl bg-green-50 p-4 mb-4" :status="session('status')" />

    <!-- Validation Errors -->
    <x-auth-validation-errors class="rounded-xl bg-red-50 p-4 mb-4 border  border-red-600" :errors="$errors" />
    <div class="mt-2">
        <form action="{{ route('register') }}" method="POST" class="space-y-6" x-data='{
            name:{{!is_null(old('name')) ? json_encode(old('name')) : 'null' }},
            email:{{!is_null(old('email')) ? json_encode(old('email')) : 'null' }},
            password:{{!is_null(old('password')) ? json_encode(old('password')): 'null' }},
            phone_number:{{!is_null(old('phone_number')) ? json_encode(old('phone_number')) : 'null' }},
            password_confirmation:{{!is_null(old('password_confirmation')) ? json_encode(old('password_confirmation')): 'null' }},
            @php foreach($authSettings->custom_registration_fields as $field){
            $id=$field['id'];
            $field='dynamicField'.$id .':';
            $field.=!is_null(old($id)) ? json_encode(old($id)).',' : 'null'.',';
            echo $field;
            }
            @endphp
            dynamicRequiredFields:"",
            field:[],
            }'>
            @csrf
            <!-- Name -->
            <div>
                <x-input id="facebook_id" class="block mt-1 w-full" type="hidden" name="facebook_id"
                :value="old('id')" required autofocus   />

                <x-label for="name" :value="__('messages.t_name')" />

                <x-filament::input.wrapper class="mt-1">
                    <x-filament::input id="name" type="text" name="name" :value="old('name')" required autofocus
                        x-model="name" />
                </x-filament::input.wrapper>
            </div>
            <!-- Email -->
            <div>
                <x-label for="email" :value="__('messages.t_email')" />
                <div class="mt-1">
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input id="email" type="text" name="email" :value="old('email')"
                            autocomplete="email" required x-model="email" />
                    </x-filament::input.wrapper>
                </div>
            </div>

            @if($loginOtpSettings->enabled)
            <!-- Phone Number -->
            <div>
                <x-label for="phone_number" :value="__('messages.t_phone_number')" />
                <div class="mt-1">
                    <x-filament::input.wrapper class="mt-1" wire:ignore>
                        <x-filament::input id="phone_number" type="tel" name="phone_number" required
                            x-model="phone_number" onblur="getPhoneNumber(event)" />
                        {{-- <input type="text" id="phone" name="phone" hidden aria-hidden="true"> --}}
                    </x-filament::input.wrapper>
                    <div class="alert alert-info text-red-500" style="display: none;"></div>
                </div>
            </div>
            @endif
            <!-- Password -->
            <div x-data="{ isPasswordRevealed: false }">
                <x-label for="password" :value="__('messages.t_password')" />
                <div class="mt-1">
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input id="password" name="password"
                            x-bind:type="isPasswordRevealed?'text':'password'" :value="old('password')"
                            autocomplete="current-password" required x-model="password" />
                        <x-slot name="suffix">
                            <button
                                :aria-label="isPasswordRevealed ? '{{__('messages.t_aria_label_hide')}}' : '{{__('messages.t_aria_label_show')}}'"
                                :aria-pressed="isPasswordRevealed.toString()"
                                type="button" @click="isPasswordRevealed = ! isPasswordRevealed"
                                class="cursor-pointer block">
                                <div x-cloak x-show="isPasswordRevealed" x-tooltip="{
                                    content: '{{__('messages.t_tooltip_hide')}}',
                                    theme: $store.theme,
                                }">
                                    <x-heroicon-s-eye-slash class="w-5 h-5 text-gray-700" aria-hidden="true" />
                                </div>
                                <div x-cloak x-show="!isPasswordRevealed"
                                x-tooltip="{
                                    content: '{{__('messages.t_tooltip_show')}}',
                                    theme: $store.theme,
                                }">
                                    <x-heroicon-s-eye class="w-5 h-5 text-gray-700" aria-hidden="true" />
                                </div>
                            </button>
                        </x-slot>
                    </x-filament::input.wrapper>
                </div>
            </div>
            <!-- Confirm Password -->
            <div x-data="{ isPasswordRevealed: false }">
                <x-label for="password_confirmation" :value="__('messages.t_confirm_password')" />
                <div class="mt-1">
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input id="password_confirmation" x-bind:type="isPasswordRevealed?'text':'password'"
                            :value="old('password_confirmation')" name="password_confirmation" required
                            x-model="password_confirmation" />
                            <x-slot name="suffix">
                                <button
                                :aria-label="isPasswordRevealed ? '{{__('messages.t_aria_label_hide')}}' : '{{__('messages.t_aria_label_show')}}'"
                                :aria-pressed="isPasswordRevealed.toString()"
                                type="button" @click="isPasswordRevealed = ! isPasswordRevealed"
                                    class="cursor-pointer block">
                                    <div x-cloak x-show="isPasswordRevealed" x-tooltip="{
                                        content: '{{__('messages.t_tooltip_hide')}}',
                                        theme: $store.theme,
                                    }">
                                        <x-heroicon-s-eye-slash class="w-5 h-5 text-gray-700" aria-hidden="true" />
                                    </div>
                                    <div x-cloak x-show="!isPasswordRevealed"
                                    x-tooltip="{
                                        content: '{{__('messages.t_tooltip_show')}}',
                                        theme: $store.theme,
                                    }">
                                        <x-heroicon-s-eye class="w-5 h-5 text-gray-700" aria-hidden="true" />
                                    </div>
                                </button>
                            </x-slot>
                    </x-filament::input.wrapper>
                </div>
            </div>


            @foreach($authSettings->custom_registration_fields as $field)
            <div >
                @if (isset($field['hidden_label']) && !$field['hidden_label'])
                <x-label :for="$field['id']" :value="__(ucfirst($field['name']))" />
                @endif
                <div class="">
                    @if($field['type'] === 'text' || $field['type'] === 'date' || $field['type'] === 'email' || $field['type'] === 'number')
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input :id="$field['id']" :type="$field['type']" :name="$field['id']"
                            :value="old($field['id'])" required="{{$field['required'] ? true : ''}}"
                            x-model="{{'dynamicField'.$field['id'] }}" />
                    </x-filament::input.wrapper>

                    @elseif($field['type'] === 'select')
                    <x-filament::input.wrapper>
                        <x-filament::input.select  :id="$field['id']" :name="$field['id']"  x-model="{{'dynamicField'.$field['id'] }}"
                            required="{{$field['required'] ? true : ''}}">
                            <option value=""  selected> {{__('messages.t_select_an_option')}}</option>
                            @foreach($field['options'] as $option)
                            <option value="{{ $option['option'] }}">{{ $option['option'] }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>

                    @elseif($field['type'] === 'checkbox')

                    <div class="flex items-center">
                        <input type="checkbox" id="{{ $field['id'] }}" name="{{ $field['id'] }}"  x-model="{{'dynamicField'.$field['id'] }}" value="1"
                            class="w-5 h-5 text-primary-600 bg-white border-gray-900 classic:border-black dark:border-white/10 rounded focus:ring-primary-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            {{ old($field['id']) ? 'checked' : '' }} required="{{$field['required'] ? true : ''}}">
                        <label for="{{ $field['id'] }}"
                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">{{
                            __(ucfirst($field['name'])) }}</label>
                    </div>


                    @elseif($field['type'] === 'radio')
                    <div class="flex gap-x-6">
                        @foreach($field['options'] as $option)
                        <div class="flex items-center ">
                            <input type="radio" id="radio-{{ $field['id'] }}-{{ $option['option'] }}"
                                name="{{ $field['id'] }}"  x-model="{{'dynamicField'.$field['id'] }}" value="{{ $option['option'] }}"
                                class="w-5 h-5 text-primary-600 bg-gray-100 border-gray-900 classic:border-black dark:border-white/10 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                {{ old($field['id'])===$option['option'] ? 'checked' : '' }}
                                required="{{$field['required'] ? true : ''}}">
                            <label for="radio-{{ $field['id'] }}-{{ $option['option'] }}"
                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">{{
                                $option['option'] }}</label>
                        </div>
                        @endforeach
                    </div>
                    @endif

                </div>
            </div>
            @endforeach


            <!-- Captcha -->
            @if($authSettings->recaptcha_enabled)
            <div>
                <div class="bg-slate-100 p-4 rounded-md text-sm text-slate-600">
                    {{ __('messages.t_recaptcha_intro') }}
                    <a href="https://policies.google.com/privacy" class="hover:text-slate-500" tabindex="-1">{{
                        __('messages.t_privacy_policy') }}</a> {{ __('messages.t_and') }}
                    <a href="https://policies.google.com/terms" class="hover:text-slate-500" tabindex="-1">{{
                        __('messages.t_terms_service') }}</a> {{ __('messages.t_recaptcha_apply') }}.
                </div>
                <div id="signup_id" style="display: none;"></div>
                {!! GoogleReCaptchaV3::render(['signup_id' => 'register']) !!}
            </div>
            @endif
            @php
            $requiredField='';
            foreach($authSettings->custom_registration_fields as $field){
            if($field['required']){
            $requiredField.='&& dynamicField'.$field['id'];
            }
            }
            @endphp
            <div>
                <x-button.secondary size="lg" class="block w-full dark:!bg-primary-600" x-bind:disabled="name && email && password && password_confirmation {{$loginOtpSettings->enabled?'&&phone_number':'' }} {{$requiredField}}

         ?false :true">
                    {{ __('messages.t_create_account_button') }}
                </x-button.secondary>
            </div>
            <div>
                <p class="text-sm text-center text-slate-600 dark:text-gray-200">
                    {{ __('messages.t_already_have_account') }}
                    <a href="{{ route('login') }}" class="font-medium  underline">
                        {{ __('messages.t_login') }}
                    </a>
                </p>
            </div>
        </form>
    </div>
</main>
    @if($loginOtpSettings->enabled)

    <script>
        const phoneInputField = document.querySelector("#phone_number");

        const phoneInput = window.intlTelInput(phoneInputField, {
            initialCountry: "{{$generalSettings->default_mobile_country??'us'}}",
            utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
                const info = document.querySelector(".alert-info");
            function getPhoneNumber(event) {
            event.preventDefault();
            const phoneNumber = phoneInput.getNumber();
            document.getElementById ("phone").value=phoneNumber;
            if (!phoneInput.isValidNumber() && phoneNumber.length>0) {
            info.style.display = "";
            info.innerHTML = `Phone number is not valid`;
            }else{
                info.innerHTML=''
            }
            }
    </script>
    @endif
</x-auth-layout>
