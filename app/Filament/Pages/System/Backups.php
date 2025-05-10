<?php

namespace App\Filament\Pages\System;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use App\Enums\Option;
use App\Jobs\CreateBackupJob;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Backups extends Page
{
    use HasPageShield;

    protected static string $view = 'filament.pages.system.backups';


    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_backup');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_system_manager');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_backup');
    }

    public static function canAccess(): bool
    {
        return userHasPermission('page_Backups');
    }
    protected function getActions(): array
    {
        return [
            Action::make('Create Backup')
                ->button()
                ->label(__('messages.t_ap_create_backup'))
                ->action('openOptionModal'),
        ];
    }

    public function openOptionModal(): void
    {
        $this->dispatch('open-modal', id: 'backup-option');
    }

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return false;
    // }

    public function create(string $option = ''): void
    {


        CreateBackupJob::dispatch(Option::from($option))
            ->afterResponse();

        $this->dispatch('close-modal', id: 'backup-option');

        Notification::make()
        ->title(__('messages.t_ap_creating_backup_notification'))
        ->success()
        ->send();
    }

}
