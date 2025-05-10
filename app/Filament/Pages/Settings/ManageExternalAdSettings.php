<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Clusters\AdPlacements;
use App\Settings\AdPlacementSettings;
use App\Settings\ExternalAdSettings;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageExternalAdSettings extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = ExternalAdSettings::class;

    protected static ?int $navigationSort = 4;
    protected static ?string $cluster = AdPlacements::class;
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_ad_detail_page');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_ad_detail_page');
    }
    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageExternalAdSettings');
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(AdPlacementSettings::class);
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
        $isDemo = Config::get('app.demo');
        return $form
            ->schema([
                Toggle::make('enable')
                    ->label(__('messages.t_ap_enable_ad'))
                    ->helperText(__('messages.t_ap_enable_ad_hint')),

                Section::make(__('messages.t_ap_ad_section_spacing'))
                    ->description(__('messages.t_ap_ad_section_spacing_desc'))
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('ad_top_spacing')
                            ->label(__('messages.t_ap_ad_top_spacing'))
                            ->numeric()
                            ->required()
                            ->helperText(__('messages.t_ap_ad_top_spacing_hint')),

                        TextInput::make('ad_right_spacing')
                            ->label(__('messages.t_ap_ad_right_spacing'))
                            ->numeric()
                            ->required()
                            ->helperText(__('messages.t_ap_ad_right_spacing_hint')),

                        TextInput::make('ad_bottom_spacing')
                            ->label(__('messages.t_ap_ad_bottom_spacing'))
                            ->numeric()
                            ->required()
                            ->helperText(__('messages.t_ap_ad_bottom_spacing_hint')),

                        TextInput::make('ad_left_spacing')
                            ->label(__('messages.t_ap_ad_left_spacing'))
                            ->numeric()
                            ->required()
                            ->helperText(__('messages.t_ap_ad_left_spacing_hint')),
                    ]),

                Textarea::make('value')
                    ->label(__('messages.t_ap_ad_script'))
                    ->required()
                    ->placeholder(__('messages.t_ap_ad_script_placeholder'))
                    ->rows(5)
                    ->disabled($isDemo)
                    ->helperText($isDemo
                        ? __('messages.t_ap_demo_restriction_hint')
                        : __('messages.t_ap_ad_script_hint')),

            ])
            ->columns(1);
    }
}
