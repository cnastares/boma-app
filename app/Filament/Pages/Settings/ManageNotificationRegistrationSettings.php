<?php

namespace App\Filament\Pages\Settings;

use App\Settings\NotificationRegistrationSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageNotificationRegistrationSettings extends SettingsPage
{
    use HasPageShield;
    
    protected static string $settings = NotificationRegistrationSettings::class;
    protected static ?int $navigationSort = 23;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageNotificationRegistrationSettings');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        return $data;
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_notification_registration');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_notification_registration');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('enable')
                    ->label(__('messages.t_ap_enable_label'))
                    ->required()
                    ->helperText(__('messages.t_ap_enable_helper_text')),

                TextInput::make('notification_email')
                    ->label(__('messages.t_ap_notification_email_label'))
                    ->required()
                    ->helperText(__('messages.t_ap_notification_email_helper_text')),

                TextInput::make('instagram_username')
                    ->label(__('messages.t_ap_instagram_username_label'))
                    ->required()
                    ->helperText(__('messages.t_ap_instagram_username_helper_text')),

                Toggle::make('auto_focus_enabled')
                    ->label(__('messages.t_ap_auto_focus_enabled_label'))
                    ->required()
                    ->helperText(__('messages.t_ap_auto_focus_enabled_helper_text')),

                FileUpload::make('banner_image')
                    ->maxSize(maxUploadFileSize())
                    ->disk('media')
                    ->image()
                    ->helperText(__('messages.t_ap_banner_image_helper_text')),
                //Todo:enable if customize only the logo in the page
                // TextInput::make('logo_width_mobile')
                //     ->label('Logo Width (Mobile)')
                //     ->numeric()
                //     ->required()
                //     ->helperText('This setting adjusts the logo width on mobile screens (below 1024px).'),

                // TextInput::make('logo_height_mobile')
                //     ->label('Logo Height (Mobile)')
                //     ->numeric()
                //     ->required()
                //     ->helperText('This setting adjusts the logo height on mobile screens (below 1024px).'),

                // TextInput::make('logo_width_desktop')
                //     ->label('Logo Width (Desktop)')
                //     ->numeric()
                //     ->required()
                //     ->helperText('This setting adjusts the logo width on desktop screens (1024px and above).'),

                // TextInput::make('logo_height_desktop')
                //     ->label('Logo Height (Desktop)')
                //     ->numeric()
                //     ->required()
                //     ->helperText('This setting adjusts the logo height on desktop screens (1024px and above).'),
            ]);
    }
}
