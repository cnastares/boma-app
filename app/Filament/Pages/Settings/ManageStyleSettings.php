<?php

namespace App\Filament\Pages\Settings;

use App\Settings\StyleSettings;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageStyleSettings extends SettingsPage
{
    use HasPageShield;

    protected static ?int $navigationSort = 9;

    protected static string $settings = StyleSettings::class;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageStyleSettings');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_style');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_style');
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(StyleSettings::class);
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
                Textarea::make('custom_style')
                ->label(__('messages.t_ap_custom_style'))
                ->placeholder(__('messages.t_ap_custom_style_placeholder'))
                ->rows(10)
                ->disabled($isDemo)
                ->hint($isDemo
                    ? __('messages.t_ap_custom_style_hint_demo')
                    : __('messages.t_ap_custom_style_hint')),
                        ])
            ->columns(1);
    }
}
