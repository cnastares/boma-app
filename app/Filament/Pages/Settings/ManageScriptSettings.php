<?php

namespace App\Filament\Pages\Settings;

use App\Settings\ScriptSettings;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageScriptSettings extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = ScriptSettings::class;

    protected static ?int $navigationSort = 8;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageScriptSettings');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_scripts');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_scripts');
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(ScriptSettings::class);
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
                Textarea::make('custom_script_head')
                    ->label(__('messages.t_ap_custom_script_head'))
                    ->placeholder(__('messages.t_ap_custom_script_head_placeholder'))
                    ->rows(5)
                    ->disabled($isDemo)
                    ->hint($isDemo ? __('messages.t_ap_demo_edit_hint') : __('messages.t_ap_custom_script_head_hint')),

                Textarea::make('custom_script_body')
                    ->label(__('messages.t_ap_custom_script_body'))
                    ->placeholder(__('messages.t_ap_custom_script_body_placeholder'))
                    ->rows(5)
                    ->disabled($isDemo)
                    ->hint($isDemo ? __('messages.t_ap_demo_edit_hint') : __('messages.t_ap_custom_script_body_hint')),

            ])
            ->columns(1);
    }
}
