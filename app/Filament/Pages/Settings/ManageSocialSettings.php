<?php

namespace App\Filament\Pages\Settings;

use App\Settings\SocialSettings;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageSocialSettings extends SettingsPage
{
    use HasPageShield;


    protected static string $settings = SocialSettings::class;

    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageSocialSettings');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_social');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_social');
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(SocialSettings::class);
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
                TextInput::make('facebook_link')
                    ->label(__('messages.t_ap_facebook_link'))
                    ->placeholder(__('messages.t_ap_facebook_placeholder'))
                    ->url()
                    ->hint(__('messages.t_ap_facebook_hint')),

                TextInput::make('twitter_link')
                    ->label(__('messages.t_ap_twitter_link'))
                    ->placeholder(__('messages.t_ap_twitter_placeholder'))
                    ->url()
                    ->hint(__('messages.t_ap_twitter_hint')),

                TextInput::make('instagram_link')
                    ->label(__('messages.t_ap_instagram_link'))
                    ->placeholder(__('messages.t_ap_instagram_placeholder'))
                    ->url()
                    ->hint(__('messages.t_ap_instagram_hint')),

                TextInput::make('linkedin_link')
                    ->label(__('messages.t_ap_linkedin_link'))
                    ->placeholder(__('messages.t_ap_linkedin_placeholder'))
                    ->url()
                    ->hint(__('messages.t_ap_linkedin_hint')),
            ]);
    }
}
