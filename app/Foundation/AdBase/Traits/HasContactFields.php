<?php
namespace App\Foundation\AdBase\Traits;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Forms\Set;
use App\Settings\PhoneSettings;
use Closure;
use Propaganistas\LaravelPhone\Rules\Phone as PhoneRule;
use Illuminate\Support\Facades\Validator;

trait HasContactFields {

    /**
     * Get display phone toggle
     * @return ToggleButtons
     */
    public function getDisplayPhoneToggle()
    {
        return ToggleButtons::make('display_phone')
            ->label(__('messages.t_display_phone_number'))
            ->live()
            ->boolean()
            ->grouped()
            ->visible(app(PhoneSettings::class)->enable_phone)
            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                // Update phone_number based on display_phone toggle
                if ($state) {
                    $this->phone_number = $this->ad?->phone_number ? $this->ad?->phone_number : auth()->user()?->phone_number;
                    $this->ad->phone_number = $this->phone_number;
                    $this->ad->save();
                    $this->checkRequiredFieldsFilled();
                    $set('phone_number', $this->phone_number);
                }
            });
    }

    /**
     * Get phone number input
     * @return PhoneInput
     */
    public function getPhoneNumberInput()
    {
        return PhoneInput::make('phone_number')
            ->initialCountry(app(GeneralSettings::class)->default_mobile_country ?? 'us')
            ->placeholder(__('messages.t_enter_phone_number'))
            ->helperText(__('messages.t_phone_number_helper'))
            ->required()
            ->live()
            ->rules([
                fn (): Closure => function (string $attribute, $value, Closure $fail) {
                    $country = 'AUTO'; // Dynamically fetch if needed

                    $validator = Validator::make(
                        [$attribute => $value],
                        [$attribute => [(new PhoneRule)->country($country)]]
                    );

                    if ($validator->fails()) {
                        $fail(__('messages.t_phone_validation')); // Fetch from translations
                    }
                },
            ])
            ->visible(app(PhoneSettings::class)->enable_phone)
            ->hidden(fn(Get $get): bool => !$get('display_phone'));
    }

    /**
     * Get same number toggle
     * @return ToggleButtons
     */
    public function getSameNumberToggle()
    {
        return ToggleButtons::make('display_whatsapp')
            ->label(__('messages.t_display_whatsapp_number'))
            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                // Update whatsapp_number when display_whatsapp is toggled
                if ($state) {
                    $this->whatsapp_number = $this->ad->whatsapp_number ? $this->ad->whatsapp_number : $get('phone_number');
                    $this->ad->whatsapp_number = $this->whatsapp_number;
                    $this->ad->save();
                    $this->checkRequiredFieldsFilled();
                    $set('whatsapp_number', $this->whatsapp_number);
                }
            })
            ->live()
            ->boolean()
            ->visible(app(PhoneSettings::class)->enable_whatsapp)
            ->grouped();
    }

    /**
     * Get whatsapp number input
     * @return PhoneInput
     */
    public function getWhatsappNumberInput()
    {
        return PhoneInput::make('whatsapp_number')
            ->initialCountry(app(GeneralSettings::class)->default_mobile_country ?? 'us')
            ->placeholder(__('messages.t_enter_phone_number'))
            ->helperText(__('messages.t_display_whatsapp_helper_text'))
            ->live()
            ->required()
            ->visible(app(PhoneSettings::class)->enable_whatsapp)
            ->rules([
                fn (): Closure => function (string $attribute, $value, Closure $fail) {
                    $country = 'AUTO'; // Dynamically fetch if needed

                    $validator = Validator::make(
                        [$attribute => $value],
                        [$attribute => [(new PhoneRule)->country($country)]]
                    );

                    if ($validator->fails()) {
                        $fail(__('messages.t_phone_validation')); // Fetch from translations
                    }
                },
            ])
             ->hidden(fn(Get $get): bool => !$get('display_whatsapp'));
    }
}
