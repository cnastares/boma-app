<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Clusters\DesignAppearance;
use App\Settings\AppearanceSettings;
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
use Filament\Forms\Components\ColorPicker;
use App\Models\SettingsProperty;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageAppearanceSettings extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = AppearanceSettings::class;

    protected static ?string $cluster = DesignAppearance::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_appearance');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_appearance_settings');
    }

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageAppearanceSettings');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(AppearanceSettings::class);
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
        return $form
            ->schema([
                ColorPicker::make('primary_color')
                    ->label(__('messages.t_ap_primary_color'))
                    ->required()
                    ->helperText(__('messages.t_ap_primary_color_help')),

                ColorPicker::make('secondary_color')
                    ->label(__('messages.t_ap_secondary_color'))
                    ->required()
                    ->helperText(__('messages.t_ap_secondary_color_help')),

                Toggle::make('enable_theme_switcher')
                    ->label(__('messages.t_ap_enable_theme_switcher'))
                    ->reactive()
                    ->afterStateUpdated(fn(Set $set) => $set('enable_contrast_toggle', false))
                    ->helperText(__('messages.t_ap_enable_theme_switcher_help')),

                Select::make('default_theme')
                    ->hidden(fn(Get $get) => $get('enable_contrast_toggle'))
                    ->label(__('messages.t_ap_default_theme'))
                    ->options([
                        'light' => __('messages.t_ap_light_theme'),
                        'dark' => __('messages.t_ap_dark_theme'),
                        'classic' => __('messages.t_ap_classic_theme')
                    ])
                    ->placeholder(__('messages.t_ap_select_default_theme'))
                    ->helperText(__('messages.t_ap_select_default_theme_help'))
                    ->required(),

                Toggle::make('enable_contrast_toggle')
                    ->label(__('messages.t_ap_enable_contrast_toggle'))
                    ->afterStateUpdated(fn(Set $set) => $set('enable_theme_switcher', false))
                    ->reactive()
                    ->helperText(__('messages.t_ap_enable_contrast_toggle_help')),

                Select::make('contrast_mode')
                    ->label(__('messages.t_ap_contrast_mode'))
                    ->visible(fn(Get $get) => $get('enable_contrast_toggle'))
                    ->options([
                        'light_dark' => __('messages.t_ap_light_dark'),
                        'light_classic' => __('messages.t_ap_light_classic'),
                        'dark_classic' => __('messages.t_ap_dark_classic'),
                    ])
                    ->placeholder(__('messages.t_ap_select_contrast_mode'))
                    ->helperText(__('messages.t_ap_select_contrast_mode_help'))
                    ->required(),

                SpatieMediaLibraryFileUpload::make('home_banner_image')
                    ->maxSize(maxUploadFileSize())
                    ->label(__('messages.t_ap_home_banner_image'))
                    ->image()
                    ->model(SettingsProperty::getInstance('appearance.home_banner_image'))
                    ->collection('banner_images')
                    ->helperText(__('messages.t_ap_home_banner_image_help')),

                Toggle::make('display_site_name')
                    ->label(__('messages.t_ap_display_site_name'))
                    ->helperText(__('messages.t_ap_display_site_name_helper_text')),
                Select::make('font')
                    ->label(__('messages.t_ap_font'))
                    ->options([
                        'Jost' => __('messages.t_ap_font_jost'),
                        'Inter' => __('messages.t_ap_font_inter'),
                        'Poppins' => __('messages.t_ap_font_poppins'),
                        'DM Sans' => __('messages.t_ap_font_dm_sans'),
                        'Manrope' => __('messages.t_ap_font_manrope'),
                    ]),
                SpatieMediaLibraryFileUpload::make('switch_to_buyer_icon')
                    ->maxSize(maxUploadFileSize())
                    ->label(__('messages.t_ap_switch_to_buyer_icon'))
                    ->collection('switch_to_buyer_icon')
                    ->image()
                    ->model(SettingsProperty::getInstance('appearance.switch_to_buyer_icon'))
                    ->helpertext(__('messages.t_ap_switch_to_buyer_icon_helpertext')),
            ]);
    }
}
