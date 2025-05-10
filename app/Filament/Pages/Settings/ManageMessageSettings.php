<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Clusters\CommunicationSettings;
use App\Settings\MessageSettings;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Toggle;

class ManageMessageSettings extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'chat-bubble-text-square';

    protected static ?string $slug = 'manage-message-settings';
    protected static ?string $cluster = CommunicationSettings::class;

    protected static string $settings = MessageSettings::class;

    protected static ?int $navigationSort = 4; // Adjust the sort order as needed


    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_message_setting');
    }
    public function getTitle(): string
    {
        return __('messages.t_ap_message_setting');
    }

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageMessageSettings');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(MessageSettings::class);
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
                Toggle::make('enable_make_offer')
                    ->label(__('messages.t_ap_enable_make_offer'))
                    ->helperText(__('messages.t_ap_enable_make_offer_description'))
            ])
            ->columns(1);
    }
}
