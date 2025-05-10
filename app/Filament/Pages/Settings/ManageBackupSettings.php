<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Config as ConfigManager;
use Illuminate\Support\Facades\Config;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageBackupSettings extends Page
{
    use HasPageShield;

    public ?array $data = [];

    protected static string $view = 'filament.pages.settings.manage-backup-settings';

    protected static ?int $navigationSort = 23;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_backup');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_backup');
    }

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageBackupSettings');
    }

    public function mount(): void
    {
        $this->data = [
            'backup_frequency' => config('backup.backup.backup_frequency'),
            'backup_time' => config('backup.backup.backup_time'),
            'retention_days' => config('backup.cleanup.default_strategy.keep_all_backups_for_days'),
            'backup_destinations' => config('backup.backup.destination.disks'),
            'notification_email' => config('backup.notifications.mail.to')
        ];
        $this->form->fill($this->data);
    }

    public function setEnvValue($values)
    {
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                setEnvironmentValue($envKey, $envValue);
            }
        }
        return true;
    }

    public function save()
    {
        try {
            $data = $this->form->getState();
            if (array_key_exists('backup_frequency', $data)) {
                ConfigManager::write('backup.backup.backup_frequency', $data['backup_frequency']);
            }
            if (array_key_exists('retention_days', $data)) {
                ConfigManager::write('backup.cleanup.default_strategy.keep_all_backups_for_days', (int) $data['retention_days']);
            }
            if (array_key_exists('backup_time', $data)) {
                ConfigManager::write('backup.backup.backup_time', $data['backup_time']);
            }
            if (array_key_exists('backup_destinations', $data)) {
                ConfigManager::write('backup.backup.destination.disks', $data['backup_destinations']);
            }
            if (array_key_exists('notification_email', $data)) {
                ConfigManager::write('backup.notifications.mail.to', $data['notification_email']);
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
        return $form->schema([
            Select::make('backup_frequency')
                ->label(__('messages.t_ap_backup_frequency_label'))
                ->options([
                    'daily' => __('messages.t_ap_backup_frequency_daily'),
                    'weekly' => __('messages.t_ap_backup_frequency_weekly'),
                    'monthly' => __('messages.t_ap_backup_frequency_monthly'),
                ])
                ->required()
                ->hint(__('messages.t_ap_backup_frequency_hint')),

            TimePicker::make('backup_time')
                ->seconds(false)
                ->label(__('messages.t_ap_backup_time_label'))
                ->required()
                ->hint(__('messages.t_ap_backup_time_hint')),

            TextInput::make('retention_days')
                ->label(__('messages.t_ap_retention_days_label'))
                ->numeric()
                ->required()
                ->hint(__('messages.t_ap_retention_days_hint')),

            Select::make('backup_destinations')
                ->multiple()
                ->required()
                ->options(array_combine(array_keys(config('filesystems.disks')), array_keys(config('filesystems.disks')))),

            TextInput::make('notification_email')
                ->label(__('messages.t_ap_notification_email_label'))
                ->email()
                ->required()
                ->hint(__('messages.t_ap_notification_email_hint')),
        ])
            ->columns(2)
            ->statePath('data');
    }
}
