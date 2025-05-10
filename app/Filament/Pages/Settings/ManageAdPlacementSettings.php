<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Clusters\AdPlacements;
use App\Settings\AdPlacementSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageAdPlacementSettings extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static string $settings = AdPlacementSettings::class;
    protected static ?string $cluster = AdPlacements::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_global_ad');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_global_ad');
    }

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageAdPlacementSettings');
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
                Textarea::make('after_header')
                    ->label(__('messages.t_ap_ad_code_after_header'))
                    ->placeholder(__('messages.t_ap_ad_code_after_header_placeholder'))
                    ->rows(5)
                    ->default('Testing')
                    ->disabled($isDemo)
                    ->hint($isDemo ? __('messages.t_ap_demo_edit_hint') : __('messages.t_ap_ad_code_after_header_hint')),

                Textarea::make('before_footer')
                    ->label(__('messages.t_ap_ad_code_before_footer'))
                    ->placeholder(__('messages.t_ap_ad_code_before_footer_placeholder'))
                    ->rows(5)
                    ->default('')
                    ->disabled($isDemo)
                    ->hint($isDemo ? __('messages.t_ap_demo_edit_hint') : __('messages.t_ap_ad_code_before_footer_hint')),

            ])
            ->columns(1);
    }
}
