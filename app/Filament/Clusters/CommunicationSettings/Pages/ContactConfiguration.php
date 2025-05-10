<?php

namespace App\Filament\Clusters\CommunicationSettings\Pages;

use App\Filament\Clusters\CommunicationSettings;
use App\Settings\PhoneSettings;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Toggle;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ContactConfiguration extends SettingsPage
{
    use HasPageShield;

    protected static ?string $cluster = CommunicationSettings::class;
    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static string $settings = PhoneSettings::class;
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ContactConfiguration');
    }
    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_contact_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_contact_configuration');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            Toggle::make('enable_phone')
                ->label(__('messages.t_ap_enable_phone'))
                ->helperText(__('messages.t_ap_enable_phone_description')),

            Toggle::make('enable_number_reveal_duplicate')
                ->label(__('messages.t_ap_enable_number_reveal_duplicate'))
                ->helperText(__('messages.t_ap_enable_number_reveal_duplicate_description')),

            Toggle::make('enable_login_user_number_reveal')
                ->label(__('messages.t_ap_enable_login_user_number_reveal'))
                ->helperText(__('messages.t_ap_enable_login_user_number_reveal_description')),

            Toggle::make('enable_whatsapp')
                ->label(__('messages.t_ap_enable_whatsapp'))
                ->helperText(__('messages.t_ap_enable_whatsapp_description')),

            ]);
    }
}
