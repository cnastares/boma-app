<?php

namespace App\Filament\Pages\Settings;

use App\Settings\PaymentSettings;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;
use Akaunting\Money\Currency as AkauntingCurrency;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Artisan;
use Filament\Forms\Get;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManagePaymentSettings extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = PaymentSettings::class;

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_payment');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_payment_settings');
    }
    public static function canAccess(): bool
    {
        return userHasPermission('page_ManagePaymentSettings');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(PaymentSettings::class);
        $filtered = [];

        foreach ($data as $key => $item) {
            // Check if the property exists in the GeneralSettings class
            if (property_exists($previousData, $key)) {
                // Get the type of the property
                $propertyType = gettype($previousData->$key);

                if ($key === 'currency') {
                    $currencySymbol = (new AkauntingCurrency($item))->getSymbol();
                    setEnvironmentValue('APP_CURRENCY', $currencySymbol);
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
        // Clear cache
        Artisan::call('config:clear');
        return $filtered;
    }

    public function form(Form $form): Form
    {
        $currenciesConfig = config('money.currencies');
        $currencyCodes = array_keys($currenciesConfig);
        return $form
            ->schema([
                Select::make('currency')
                    ->label(__('messages.t_ap_currency'))
                    ->options(array_combine($currencyCodes, $currencyCodes))
                    ->searchable()
                    ->placeholder(__('messages.t_ap_currency_placeholder'))
                    ->required()
                    ->helperText(__('messages.t_ap_currency_helper')),

                Toggle::make('enable_tax')
                    ->label(__('messages.t_ap_enable_tax'))
                    ->live()
                    ->columnSpanFull()
                    ->helperText(__('messages.t_ap_enable_tax_helper')),

                Select::make('tax_type')
                    ->label(__('messages.t_ap_tax_type'))
                    ->visible(fn(Get $get): bool => $get('enable_tax'))
                    ->options([
                        'percentage' => __('messages.t_ap_percentage'),
                        'fixed' => __('messages.t_ap_fixed')
                    ])
                    ->helperText(__('messages.t_ap_tax_type_helper')),

                TextInput::make('tax_rate')
                    ->label(__('messages.t_ap_tax_rate'))
                    ->visible(fn(Get $get): bool => $get('enable_tax'))
                    ->numeric()
                    ->helperText(__('messages.t_ap_tax_rate_helper')),

                Select::make('currency_locale')
                    ->helperText(__('messages.t_ap_currency_locale_helper'))
                    ->options(CURRENCY_LOCALE)
                    ->searchable(),
                Toggle::make('display_currency_after_price')
                    ->label(__('messages.t_display_currency_after_price'))
                    ->live()
                    ->columnSpanFull()
                    ->helperText(__('messages.t_display_currency_after_price_helper')),
            ]);
    }
}
