<?php

namespace App\Filament\Clusters\Locations\Pages;

use App\Settings\LocationSettings;
use App\Filament\Clusters\Locations;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;
use App\Models\Country;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Toggle;

class LocationConfig extends SettingsPage
{
    use HasPageShield;

    protected static ?string $cluster = Locations::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = LocationSettings::class;

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return userHasPermission('page_LocationConfig');
    }
    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_location_configuration');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_location_configuration');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['google_api_key'] = config('google.api_key');

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(LocationSettings::class);
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
            // If the key is stripe_api_key or stripe_secret_key, write it to config
            if ($key === 'google_api_key') {
                setEnvironmentValue('GOOGLE_API_KEY', $item);
            }
            // For other cases, just copy the item as is
            $filtered[$key] = $item;
        }
        return $filtered;
    }

    public function form(Form $form): Form
    {
        $isDemo = Config::get('app.demo');

        return $form
            ->schema([
                Select::make('location_source')
                    ->label(__('messages.t_ap_select_location_source'))
                    ->options([
                        'openstreet' => __('messages.t_ap_open_street_map'),
                        'google' => __('messages.t_ap_google_maps'),
                        'custom' => __('messages.t_ap_custom_location'),
                    ])
                    ->default('openstreet')
                    ->helperText(__('messages.t_ap_location_source_helper_text'))
                    ->required(),
                // $isDemo ?
                // Placeholder::make('google_api_key')
                //     ->content('*****')
                //     ->hint(__('messages.t_ap_hidden_due_to_demo_mode')) :
                // TextInput::make('google_api_key')
                //     ->label(__('messages.t_ap_google_maps_api_key'))
                //     ->placeholder(__('messages.t_ap_enter_google_maps_api_key'))
                //     ->helperText(__('messages.t_ap_google_api_key_description'))
                //     ->required(),

                Select::make('allowed_countries')
                    ->multiple()
                    ->label(__('messages.t_ap_allowed_countries'))
                    ->searchable()
                    ->options(Country::all()->pluck('name', 'iso2'))
                    ->helperText(__('messages.t_ap_allowed_countries_description'))
                    ->live(),

                Select::make('default_country')
                    ->label(__('messages.t_ap_default_country'))
                    ->searchable()
                    ->options(
                        fn($get) =>
                        !empty($get('allowed_countries'))
                        ? Country::whereIn('iso2', $get('allowed_countries'))->pluck('name', 'iso2')
                        : Country::all()->pluck('name', 'iso2')
                    )
                    ->placeholder(__('messages.t_ap_select_default_country'))
                    ->helperText(__('messages.t_ap_default_country_description')),

                TextInput::make('search_radius')
                    ->numeric()
                    ->label(__('messages.t_ap_search_radius_km'))
                    ->placeholder(__('messages.t_ap_enter_search_radius_km'))
                    ->helperText(__('messages.t_ap_search_radius_description'))
                    ->required(),
                Toggle::make('enable_location_auto_detection')
                    ->label(__('messages.t_ap_location_auto_detect'))
                    ->helperText(__('messages.t_ap_location_auto_detect_helper'))
                    ->default(false)
                    ->live()
            ]);
    }
}
