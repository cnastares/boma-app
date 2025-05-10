<?php

namespace App\Filament\Clusters\AdPlacements\Pages;

use App\Filament\Clusters\AdPlacements;
use App\Settings\CategoryAdSettings;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageCategoryAdSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = CategoryAdSettings::class;

    protected static ?string $cluster = AdPlacements::class;

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_category_ad_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_category_ad_settings');
    }

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageCategoryAdSettings');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(static::$settings);
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
                TextInput::make('image_height')
                ->label(__('messages.t_ap_image_height'))
                ->helperText(__('messages.t_ap_set_image_height'))
                ->required()
                ->numeric()
                ->minValue(1)
                ->maxValue(2000)
                ->placeholder(__('messages.t_ap_enter_image_height')),

                TextInput::make('image_width')
                ->label(__('messages.t_ap_image_width'))
                ->helperText(__('messages.t_ap_set_image_width'))
                ->required()
                ->numeric()
                ->minValue(1)
                ->maxValue(2000)
                ->placeholder(__('messages.t_ap_enter_image_width')),
            ]);
    }
}
