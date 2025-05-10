<?php

namespace App\Filament\Pages\Settings;

use App\Settings\VerificationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageVerification extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = VerificationSettings::class;
    protected static ?int $navigationSort = 9;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageVerification');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_verification_settings');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_verification_settings');
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            Toggle::make('hide_attachment')
                ->label(__('messages.t_ap_hide_attachment'))
                ->helperText(__('messages.t_ap_hide_attachment_hint')),

            Repeater::make('document_types')
                ->schema([
                    Select::make('type')
                        ->label(__('messages.t_ap_document_type'))
                        ->options([
                            'id' => __('messages.t_government_issued_id'),
                            'driver_license' => __('messages.t_driver_license'),
                            'passport' => __('messages.t_passport')
                        ])
                        ->required(),
                    Toggle::make('enable')
                        ->label(__('messages.t_ap_enable')),
                    Toggle::make('selfie_required')
                        ->label(__('messages.t_ap_selfie_required')),
                    Toggle::make('back_required')
                        ->label(__('messages.t_ap_back_required')),
                ])
                ->columns(3)
                ->addActionLabel(__('messages.t_ap_add_document_type'))
                ->deletable(false)
                ->addable(false)
                ->columnSpanFull()
                ->reorderable(),
            ]);
    }
}

