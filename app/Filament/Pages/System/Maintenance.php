<?php

namespace App\Filament\Pages\System;

use Illuminate\Support\Facades\Auth;
use App\Notifications\Admin\SiteIsDown;
use App\Settings\MaintenanceSettings;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Config;
use Exception;
use Illuminate\Support\HtmlString;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Maintenance extends SettingsPage
{
    use HasPageShield;


    protected ?string $subheading = 'Please proceed with caution. Copy the secret URL for site access during maintenance. This URL will also be emailed for secure entry.';

    protected static string $settings = MaintenanceSettings::class;

    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        return userHasPermission('page_Maintenance');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_maintenance');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_system_manager');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_maintenance_mode');
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Check if the application is in maintenance mode
        $data['maintenance_mode'] = app()->isDownForMaintenance() ? 'maintenance' : 'live';
        if ($data['maintenance_mode'] === 'live') {
            // Generate a new secret
            $secret = (string) Str::uuid()->toString();
            $data['secret'] = $secret;
        }

        return $data;
    }


    protected function afterSave(): void
    {

        $data = $this->form->getState();

        // Handle maintenance mode activation/deactivation
        if ($data['maintenance_mode'] === 'maintenance') {

            try {
                // Set maintenance mode settings
                Config::write('maintenance.headline', str_replace(["'", '"'], '', $data['headline']));
                Config::write('maintenance.message', str_replace(["'", '"'], '', $data['message']));
                Config::write('maintenance.secret', $data['secret']);
            } catch (Exception $ex) {
                dd($ex->getMessage());
            }

            // Notify the logged-in user
            $user = Auth::user();
            try {
                if ($user) {
                    $user->notify(new SiteIsDown($data['secret']));
                }
            } catch (Exception $ex) {
            }


            // Put site in maintenance mode with the new secret
            Artisan::call('down', ['--secret' => $data['secret']]);
        } else if ($data['maintenance_mode'] === 'live') {
            // Bring site back up
            Artisan::call('up');
        }

        // Clear config cache if needed
        Artisan::call('config:clear');
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(MaintenanceSettings::class);
        $filtered = [];

        foreach ($data as $key => $item) {
            if (property_exists($previousData, $key)) {
                $propertyType = gettype($previousData->$key);

                if (is_null($item) && $propertyType === 'string') {
                    $filtered[$key] = '';
                    continue;
                }
            }
            $filtered[$key] = $item;
        }
        return $filtered;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('maintenance_mode')
                    ->label(__('messages.t_ap_application_status'))
                    ->options([
                        'live' => __('messages.t_ap_application_is_live'),
                        'maintenance' => __('messages.t_ap_application_under_maintenance'),
                    ])
                    ->disabled(config('app.demo'))
                    ->required()
                    ->hint(__('messages.t_ap_application_status_hint')),

                TextInput::make('headline')
                    ->label(__('messages.t_ap_headline'))
                    ->placeholder(__('messages.t_ap_enter_headline'))
                    ->required(),

                Textarea::make('message')
                    ->label(__('messages.t_ap_message'))
                    ->placeholder(__('messages.t_ap_enter_maintenance_message'))
                    ->required(),

                TextInput::make('secret')
                    ->label(__('messages.t_ap_secret_key'))
                    ->placeholder(__('messages.t_ap_secret_key_placeholder'))
                    ->readOnly(),

                Placeholder::make('maintenanceUrl')
                    ->label(__('messages.t_ap_maintenance_url'))
                    ->content(function (Get $get): string {
                        $secret = $get('secret');
                        return $secret ? url('/') . "/{$secret}" : __('messages.t_ap_access_url_placeholder');
                    })
                    ->columnSpanFull()
                    ->helperText(new HtmlString(__('messages.t_ap_maintenance_url_helper')))
            ]);
    }
}
