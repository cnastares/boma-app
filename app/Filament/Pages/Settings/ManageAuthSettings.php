<?php

namespace App\Filament\Pages\Settings;

use App\Settings\AuthSettings;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;
use Filament\Forms\Get;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageAuthSettings extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = AuthSettings::class;

    protected static ?int $navigationSort = 11;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_auth');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_auth');
    }
    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageAuthSettings');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['recaptcha_site_key'] = config('recaptcha.site_key');
        $data['recaptcha_secret_key'] = config('recaptcha.secret_key');

        $data['google_client_id'] = config('google.client_id');
        $data['google_client_secret'] = config('google.client_secret');

        $data['facebook_app_id'] = config('facebook.client_id');
        $data['facebook_app_secret'] = config('facebook.client_secret');

        return $data;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {

        $previousData = app(AuthSettings::class);
        $filtered = [];

        foreach ($data as $key => $item) {
            // Check if the property exists in the GeneralSettings class
            if (property_exists($previousData, $key)) {
                // Get the type of the property
                $propertyType = gettype($previousData->$key);

                // If the item is null and the property type is string, set it to an empty string
                if (is_null($item) && $propertyType === 'string') {
                    $filtered[$key] = '';
                    continue;
                }
            }
            // For other cases, just copy the item as is
            $filtered[$key] = $item;

            // If the key is recaptcha_site_key or recaptcha_secret_key, write it to config
            if ($key === 'recaptcha_site_key') {
                setEnvironmentValue('RECAPTCHA_SITE_KEY', $item);
            } elseif ($key === 'recaptcha_secret_key') {
                setEnvironmentValue('RECAPTCHA_SECRET_KEY', $item);
            } elseif ($key === 'google_client_id') {
                setEnvironmentValue('GOOGLE_CLIENT_ID', $item);
            } elseif ($key === 'google_client_secret') {
                setEnvironmentValue('GOOGLE_CLIENT_SECRET', $item);
            }elseif ($key === 'facebook_app_id') {
                setEnvironmentValue('FACEBOOK_CLIENT_ID', $item);
            } elseif ($key === 'facebook_app_secret') {
                setEnvironmentValue('FACEBOOK_CLIENT_SECRET', $item);
            }
        }
        return $filtered;
    }

    public function form(Form $form): Form
    {
        $isDemo = Config::get('app.demo');
        return $form
            ->schema([
                Toggle::make('recaptcha_enabled')
                    ->label(__('messages.t_ap_enable_recaptcha'))
                    ->live()
                    ->columnSpanFull(),

                $isDemo
                ? Placeholder::make('recaptcha_site_key')
                    ->content('*****')
                    ->visible(fn(Get $get): bool => $get('recaptcha_enabled'))
                    ->hint(__('messages.t_ap_hidden_demo_mode'))
                : TextInput::make('recaptcha_site_key')
                    ->label(__('messages.t_ap_recaptcha_site_key'))
                    ->placeholder(__('messages.t_ap_recaptcha_site_key_placeholder'))
                    ->visible(fn(Get $get): bool => $get('recaptcha_enabled'))
                    ->required()
                    ->hint(__('messages.t_ap_recaptcha_site_key_hint')),

                $isDemo
                ? Placeholder::make('recaptcha_secret_key')
                    ->content('*****')
                    ->visible(fn(Get $get): bool => $get('recaptcha_enabled'))
                    ->hint(__('messages.t_ap_hidden_demo_mode'))
                : TextInput::make('recaptcha_secret_key')
                    ->label(__('messages.t_ap_recaptcha_secret_key'))
                    ->placeholder(__('messages.t_ap_recaptcha_secret_key_placeholder'))
                    ->visible(fn(Get $get): bool => $get('recaptcha_enabled'))
                    ->required()
                    ->hint(__('messages.t_ap_recaptcha_secret_key_hint')),

                Grid::make()
                    ->schema([
                        Toggle::make('enable_google_login')
                            ->label(__('messages.t_ap_enable_google_login'))
                            ->live()
                            ->columnSpanFull(),

                        $isDemo
                        ? Placeholder::make('google_client_id')
                            ->content('*****')
                            ->visible(fn(Get $get): bool => $get('enable_google_login'))
                            ->hint(__('messages.t_ap_hidden_demo_mode'))
                        : TextInput::make('google_client_id')
                            ->label(__('messages.t_ap_google_client_id'))
                            ->visible(fn(Get $get): bool => $get('enable_google_login')),

                        $isDemo
                        ? Placeholder::make('google_client_secret')
                            ->content('*****')
                            ->visible(fn(Get $get): bool => $get('enable_google_login'))
                            ->hint(__('messages.t_ap_hidden_demo_mode'))
                        : TextInput::make('google_client_secret')
                            ->label(__('messages.t_ap_google_client_secret'))
                            ->visible(fn(Get $get): bool => $get('enable_google_login')),
                    ]),

                Grid::make()
                    ->schema([
                        Toggle::make('enable_facebook_login')
                            ->label(__('messages.t_ap_enable_facebook_login'))
                            ->live()
                            ->columnSpanFull(),

                        // Facebook App ID Field
                        $isDemo
                            ? Placeholder::make('facebook_app_id')
                                ->content('*****')
                                ->visible(fn(Get $get): bool => $get('enable_facebook_login'))
                                ->hint(__('messages.t_ap_hidden_demo_mode'))
                            : TextInput::make('facebook_app_id')
                                ->label(__('messages.t_ap_facebook_app_id'))
                                ->placeholder(__('messages.t_ap_facebook_app_id_placeholder'))
                                ->visible(fn(Get $get): bool => $get('enable_facebook_login'))
                                ->required()
                                ->hint(__('messages.t_ap_facebook_app_id_hint')),

                        // Facebook App Secret Field
                        $isDemo
                            ? Placeholder::make('facebook_app_secret')
                                ->content('*****')
                                ->visible(fn(Get $get): bool => $get('enable_facebook_login'))
                                ->hint(__('messages.t_ap_hidden_demo_mode'))
                            : TextInput::make('facebook_app_secret')
                                ->label(__('messages.t_ap_facebook_app_secret'))
                                ->placeholder(__('messages.t_ap_facebook_app_secret_placeholder'))
                                ->visible(fn(Get $get): bool => $get('enable_facebook_login'))
                                ->required()
                                ->hint(__('messages.t_ap_facebook_app_secret_hint')),
                    ]),
                Repeater::make('custom_registration_fields')
                    ->id('custom_registration_fields')
                    ->label(__('messages.t_ap_dynamic_fields'))
                    ->schema([
                        Hidden::make('id')->default(uid(18)),
                        TextInput::make('name')
                            ->required()
                            ->label(__('messages.t_ap_field_name'))
                            ->placeholder(__('messages.t_ap_field_name_placeholder')),

                        Select::make('type')
                            ->required()
                            ->label(__('messages.t_ap_field_type'))
                            ->options([
                                'text' => __('messages.t_ap_text'),
                                'date' => __('messages.t_ap_date'),
                                'radio' => __('messages.t_ap_radio'),
                                'checkbox' => __('messages.t_ap_checkbox'),
                                'select' => __('messages.t_ap_select'),
                                'number' => __('messages.t_ap_number'),
                            ])
                            ->live()
                            ->placeholder(__('messages.t_ap_field_type_placeholder')),

                        TextInput::make('max_digits')
                            ->helperText(__('messages.t_ap_max_digits_hint'))
                            ->numeric()
                            ->visible(fn(Get $get): bool => $get('type') == 'number'),

                        TextInput::make('min_digits')
                            ->helperText(__('messages.t_ap_min_digits_hint'))
                            ->numeric()
                            ->visible(fn(Get $get): bool => $get('type') == 'number'),

                        Toggle::make('hidden_label')
                            ->helperText(__('messages.t_ap_hidden_label_hint'))
                            ->inline(false),

                        Toggle::make('is_unique')
                            ->visible(fn(Get $get): bool => in_array($get('type'), ['number', 'text']))
                            ->label(__('messages.t_ap_unique'))
                            ->helperText(__('messages.t_ap_unique_hint')),

                        Repeater::make('options')
                            ->label(__('messages.t_ap_options'))
                            ->schema([
                                TextInput::make('option')
                                    ->label(__('messages.t_ap_option')),
                            ])
                            ->visible(fn(Get $get): bool => in_array($get('type'), ['radio', 'select']))
                            ->hint(__('messages.t_ap_options_hint')),

                        Checkbox::make('required')
                            ->label(__('messages.t_ap_required'))
                            ->helperText(__('messages.t_ap_required_hint')),
                    ])
                    ->columns(2)
                    ->columnSpan(2)
                    ->hint(__('messages.t_ap_custom_registration_fields_hint')),

            ]);
    }
}
