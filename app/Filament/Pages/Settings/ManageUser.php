<?php

namespace App\Filament\Pages\Settings;

use App\Settings\UserSettings;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageUser extends SettingsPage
{
    use HasPageShield;

    
    protected static string $settings = UserSettings::class;
    protected static ?int $navigationSort = 18;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageUser');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_user_settings');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_user_settings');
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('can_edit_registered_email')
                ->helperText(__('messages.t_ap_can_edit_registered_email_helper')),

            TextInput::make('max_character')
                ->label(__('messages.t_ap_max_character_label'))
                ->numeric()
                ->minValue(1)
                ->helperText(__('messages.t_ap_max_character_helper'))
            ]);
    }
}
