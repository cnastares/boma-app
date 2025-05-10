<?php

namespace App\Filament\Pages\Settings;

use App\Models\Country;
use App\Models\Language;
use App\Models\SettingsProperty;
use App\Settings\GeneralSettings;
use App\Settings\LocationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\SettingsPage;
use Config;
use Filament\Forms\Components\RichEditor;
use Tapp\FilamentTimezoneField\Forms\Components\TimezoneSelect;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageGeneralSettings extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = GeneralSettings::class;

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageGeneralSettings');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_general');
    }


    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }


    public function getTitle(): string
    {
        return __('messages.t_ap_general_settings');
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(GeneralSettings::class);
        $filtered = [];

        foreach ($data as $key => $item) {
            // Check if the property exists in the GeneralSettings class
            if (property_exists($previousData, $key)) {
                // Get the type of the property
                $propertyType = gettype($previousData->$key);
                if ($key === 'site_name') {
                    Config::write('app.name', $item);
                }

                if ($key === 'default_language') {
                    Config::write('app.locale', $item);
                }

                if ($key === 'timezone') {
                    Config::write('app.timezone', $item);
                }

                // If the item is null and the property type is string, set it to an empty string
                if (is_null($item) && $propertyType === 'string') {
                    $filtered[$key] = '';
                    continue;
                }
            }
            // For other cases, just copy the item as is
            $filtered[$key] = $item;
        }
        return $filtered;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('site_name')
                    ->label(__('messages.t_ap_site_name'))
                    ->placeholder(__('messages.t_ap_site_name_placeholder'))
                    ->required(),

                TextInput::make('separator')
                    ->label(__('messages.t_ap_separator'))
                    ->placeholder(__('messages.t_ap_separator_placeholder'))
                    ->helperText(__('messages.t_ap_separator_helper')),

                TextInput::make('site_tagline')
                    ->label(__('messages.t_ap_site_tagline'))
                    ->placeholder(__('messages.t_ap_site_tagline_placeholder'))
                    ->required(),

                RichEditor::make('site_description')
                    ->label(__('messages.t_ap_site_description'))
                    ->required()
                    ->columnSpanFull(),

                Grid::make(5)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo_path')
                            ->maxSize(maxUploadFileSize())
                            ->label(__('messages.t_ap_upload_logo'))
                            ->collection('logo')
                            ->columnSpan(3)
                            ->image()
                            ->model(SettingsProperty::getInstance('general.logo_path'))
                            ->hint(__('messages.t_ap_upload_logo_hint')),

                        TextInput::make('logo_height_mobile')
                            ->label(__('messages.t_ap_mobile_logo_height'))
                            ->numeric()
                            ->columnSpan([
                                'default' => 3,
                                'sm' => 3,
                                '2xl' => 1,
                            ])
                            ->inputMode('decimal')
                            ->placeholder(__('messages.t_ap_mobile_logo_height_placeholder'))
                            ->helperText(__('messages.t_ap_mobile_logo_height_helper')),

                        TextInput::make('logo_height_desktop')
                            ->label(__('messages.t_ap_desktop_logo_height'))
                            ->numeric()
                            ->columnSpan([
                                'default' => 3,
                                'sm' => 3,
                                '2xl' => 1,
                            ])
                            ->inputMode('decimal')
                            ->placeholder(__('messages.t_ap_desktop_logo_height_placeholder'))
                            ->helperText(__('messages.t_ap_desktop_logo_height_helper')),
                    ]),

                SpatieMediaLibraryFileUpload::make('favicon_path')
                    ->maxSize(maxUploadFileSize())
                    ->label(__('messages.t_ap_upload_favicon'))
                    ->collection('favicon')
                    ->image()
                    ->model(SettingsProperty::getInstance('general.favicon_path'))
                    ->hint(__('messages.t_ap_upload_favicon_hint')),

                Select::make('default_language')
                    ->label(__('messages.t_ap_default_language'))
                    ->selectablePlaceholder(false)
                    ->options(Language::all()->pluck('title', 'lang_code'))
                    ->placeholder(__('messages.t_ap_select_language'))
                    ->hint(__('messages.t_ap_select_language_hint')),

                TimezoneSelect::make('timezone')
                    ->searchable()
                    ->required(),

                Grid::make()
                    ->schema([
                        TextInput::make('contact_email')
                            ->label(__('messages.t_ap_contact_email'))
                            ->email()
                            ->placeholder(__('messages.t_ap_contact_email_placeholder')),

                        TextInput::make('contact_phone')
                            ->label(__('messages.t_ap_contact_phone'))
                            ->tel()
                            ->placeholder(__('messages.t_ap_contact_phone_placeholder')),
                    ]),

                Textarea::make('contact_address')
                    ->label(__('messages.t_ap_contact_address'))
                    ->placeholder(__('messages.t_ap_contact_address_placeholder'))
                    ->rows(3),

                Toggle::make('europa_cookie_consent_enabled')
                    ->label('Enable EU Cookie Consent')
                    ->helperText('Comply with EU regulations by enabling cookie consent')
                    ->live(),

                Grid::make()
                    ->schema([
                        Toggle::make('cookie_consent_enabled')
                            ->label(__('messages.t_ap_enable_cookie_consent'))
                            ->live(),

                        RichEditor::make('cookie_consent_message')
                            ->label(__('messages.t_ap_cookie_consent_message'))
                            ->visible(fn(Get $get): bool => $get('cookie_consent_enabled'))
                            ->placeholder(__('messages.t_ap_cookie_consent_message_placeholder')),

                        TextInput::make('cookie_consent_agree')
                            ->label(__('messages.t_ap_cookie_consent_agree'))
                            ->visible(fn(Get $get): bool => $get('cookie_consent_enabled'))
                            ->placeholder(__('messages.t_ap_cookie_consent_agree_placeholder'))
                    ]),

                Select::make('default_mobile_country')
                    ->label(__('messages.t_ap_default_mobile_country'))
                    ->searchable()
                    ->options(function () {
                        $allowedCountries = app(LocationSettings::class)->allowed_countries ?? [];

                        return !empty($allowedCountries) ?
                            Country::whereIn('iso2', $allowedCountries)->pluck('name', 'iso2')->toArray() :
                            Country::pluck('name', 'iso2')->toArray();
                    })
                    ->placeholder(__('messages.t_ap_select_default_country'))
                    ->helperText(__('messages.t_ap_default_country_helper')),

            ]);
    }
}
