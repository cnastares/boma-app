<?php

namespace App\Filament\Pages\Settings;

use App\Settings\DashBoardSettings;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageDashBoardSettings extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = DashBoardSettings::class;
    protected static ?int $navigationSort = 24;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageDashBoardSettings');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_dashboard_settings');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_dashboard_settings');
    }

    public static function isDiscovered(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('enable_move_chart_to_bottom')
                    ->label(__('messages.t_ap_enable_move_chart_to_bottom'))
                    ->helperText(__('messages.t_ap_enable_move_chart_to_bottom_helper')),
            ]);
    }
}
