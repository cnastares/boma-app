<?php

namespace App\Filament\Pages\Settings;

use App\Models\SettingsProperty;
use App\Settings\EmailSettings;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Config;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageEmailSettings extends Page
{
    use HasPageShield;

    public ?array $data = [];

    protected static string $view = 'filament.pages.settings.manage-email-settings';

    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageEmailSettings');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_email');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_email');
    }
    public function getEmailSettingsProperty()
    {
        return app(EmailSettings::class);
    }
    public function mount(): void
    {
        $this->data = [
            'smtp_host' => config('mail.mailers.smtp.host'),
            'smtp_port' => config('mail.mailers.smtp.port'),
            'smtp_password' => config('mail.mailers.smtp.password'),
            'smtp_user' => config('mail.mailers.smtp.username'),
            'from_email' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'display_logo' => $this->emailSettings->display_logo
        ];
        $this->form->fill($this->data);
    }



    public function save()
    {
        try {
            $data = $this->form->getState();
            if (array_key_exists('smtp_host', $data)) {
                setEnvironmentValue('MAIL_HOST', $data['smtp_host']);
            }
            if (array_key_exists('smtp_user', $data)) {
                setEnvironmentValue('MAIL_USERNAME', $data['smtp_user']);
            }
            if (array_key_exists('smtp_password', $data)) {
                setEnvironmentValue('MAIL_PASSWORD', $data['smtp_password']);
            }
            if (array_key_exists('smtp_port', $data)) {
                setEnvironmentValue('MAIL_PORT', $data['smtp_port']);
            }
            if (array_key_exists('from_email', $data)) {
                setEnvironmentValue('MAIL_FROM_ADDRESS', $data['from_email']);
            }
            if (array_key_exists('from_name', $data)) {
                setEnvironmentValue('MAIL_FROM_NAME', $data['from_name']);
            }
            if (array_key_exists('display_logo', $data)) {
                $this->emailSettings->display_logo = $data['display_logo'];
                $this->emailSettings->save();
            }
            // Clear cache
            Artisan::call('config:clear');

            Notification::make()
                ->title(__('messages.t_saved'))
                ->success()
                ->send();
        } catch (\Throwable $th) {

            // Error
            Notification::make()
                ->title(__('messages.t_common_error'))
                ->danger()
                ->send();
            throw $th;
        }
    }

    public function form(Form $form): Form
    {
        $isDemo = Config::get('app.demo');

        return $form->schema([
            $isDemo ?
            Placeholder::make('smtp_host')
            ->label(__('messages.t_ap_smtp_host'))
            ->content('*****')
            ->hint(__('messages.t_ap_demo_account_hint')) :
            TextInput::make('smtp_host')
            ->label(__('messages.t_ap_smtp_host'))
            ->placeholder(__('messages.t_ap_smtp_host_placeholder'))
            ->required(),

        $isDemo ?
            Placeholder::make('smtp_user')
            ->label(__('messages.t_ap_smtp_username'))
            ->content('*****')
            ->hint(__('messages.t_ap_demo_account_hint')) :
            TextInput::make('smtp_user')
            ->label(__('messages.t_ap_smtp_username'))
            ->placeholder(__('messages.t_ap_smtp_username_placeholder'))
            ->required(),

        $isDemo ?
            Placeholder::make('smtp_password')
            ->label(__('messages.t_ap_smtp_password'))
            ->content('*****')
            ->hint(__('messages.t_ap_demo_account_hint')) :
            TextInput::make('smtp_password')
            ->label(__('messages.t_ap_smtp_password'))
            ->placeholder(__('messages.t_ap_smtp_password_placeholder'))
            ->password()
            ->required(),

        $isDemo ?
            Placeholder::make('smtp_port')
            ->label(__('messages.t_ap_smtp_port'))
            ->content('*****')
            ->hint(__('messages.t_ap_demo_account_hint')) :
            TextInput::make('smtp_port')
            ->numeric()
            ->label(__('messages.t_ap_smtp_port'))
            ->placeholder(__('messages.t_ap_smtp_port_placeholder'))
            ->minValue(1)
            ->maxValue(65535)
            ->required(),

        $isDemo ?
            Placeholder::make('from_email')
            ->label(__('messages.t_ap_from_email'))
            ->content('*****')
            ->hint(__('messages.t_ap_demo_account_hint')) :
            TextInput::make('from_email')
            ->label(__('messages.t_ap_from_email'))
            ->placeholder(__('messages.t_ap_from_email_placeholder'))
            ->email()
            ->required(),

        $isDemo ?
            Placeholder::make('from_name')
            ->label(__('messages.t_ap_from_name'))
            ->content('*****')
            ->hint(__('messages.t_ap_demo_account_hint')) :
            TextInput::make('from_name')
            ->label(__('messages.t_ap_from_name'))
            ->placeholder(__('messages.t_ap_from_name_placeholder'))
            ->required(),

        Toggle::make('display_logo')
            ->helperText(__('messages.t_ap_display_logo_helper')),

        SpatieMediaLibraryFileUpload::make('email_logo')
            ->maxSize(maxUploadFileSize())
            ->label(__('messages.t_ap_upload_email_logo'))
            ->collection('email_logo')
            ->columnSpan('full')
            ->image()
            ->model(SettingsProperty::getInstance('email.email_logo'))
            ->hint(__('messages.t_ap_upload_email_logo_hint')),
        ])
        ->columns([
            'sm' => 1, // One column for small screens
            'md' => 2, // Two columns for medium screens
            'lg' => 3, // Three columns for large screens
        ])
        ->statePath('data');
    }
}
