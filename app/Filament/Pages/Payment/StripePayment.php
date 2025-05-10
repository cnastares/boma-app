<?php

namespace App\Filament\Pages\Payment;

use App\Settings\StripeSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Models\SettingsProperty;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Facades\Config;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class StripePayment extends SettingsPage
{
    use HasPageShield;

    protected static ?string $slug = 'manage-stripe-settings';

    protected static string $settings = StripeSettings::class;

    protected static ?int $navigationSort = 10;


    public static function canAccess(): bool
    {
        return userHasPermission('page_StripePayment');
    }
    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_stripe_settings');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_payment_gateways');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_stripe_settings');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(StripeSettings::class);
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
        }
        return $filtered;
    }


    public function form(Form $form): Form
    {
        $currenciesConfig = config('money.currencies');
        $currencyCodes = array_keys($currenciesConfig);
        $isDemo = Config::get('app.demo');
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('messages.t_ap_stripe_name_label'))
                    ->required()
                    ->helperText(__('messages.t_ap_stripe_name_helper_text')),

                Toggle::make('status')
                    ->label(__('messages.t_ap_enable_stripe_label'))
                    ->helperText(__('messages.t_ap_enable_stripe_helper_text')),

                SpatieMediaLibraryFileUpload::make('logo')
                    ->maxSize(maxUploadFileSize())
                    ->label(__('messages.t_ap_stripe_logo_label'))
                    ->collection('stripe')
                    ->hidden()
                    ->visibility('public')
                    ->image()
                    ->model(
                        SettingsProperty::getInstance('stripe.logo'),
                    )
                    ->helperText(__('messages.t_ap_stripe_logo_helper_text')),

                Select::make('currency')
                    ->label(__('messages.t_ap_default_currency_label'))
                    ->options(array_combine($currencyCodes, $currencyCodes))
                    ->required()
                    ->helperText(__('messages.t_ap_stripe_default_currency_helper_text')),

                $isDemo ?
                Placeholder::make('public_key')
                    ->label(__('messages.t_ap_stripe_public_key_label'))
                    ->content('*****')
                    ->hint(__('messages.t_ap_stripe_public_key_hint')) :
                TextInput::make('public_key')
                    ->label(__('messages.t_ap_stripe_public_key_label'))
                    ->required()
                    ->helperText(__('messages.t_ap_stripe_public_key_helper_text')),

                $isDemo ?
                Placeholder::make('secret_key')
                    ->label(__('messages.t_ap_stripe_secret_key_label'))
                    ->content('*****')
                    ->hint(__('messages.t_ap_stripe_secret_key_hint')) :
                TextInput::make('secret_key')
                    ->label(__('messages.t_ap_stripe_secret_key_label'))
                    ->required()
                    ->helperText(__('messages.t_ap_stripe_secret_key_helper_text')),

                TextInput::make('exchange_rate')
                    ->label(__('messages.t_ap_exchange_rate_label'))
                    ->numeric()
                    ->helperText(__('messages.t_ap_exchange_rate_helper_text')),
            ])
            ->columns(2);
    }
}
