<?php

namespace App\Filament\Pages\Settings;

use App\Settings\PwaSettings;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SettingsPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManagePwaSettings extends SettingsPage
{
    use HasPageShield;
    
    protected static string $settings = PwaSettings::class;
    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManagePwaSettings');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        return $data;
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_pwa_settings');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_pwa_settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('messages.t_ap_name_label'))
                    ->required()
                    ->placeholder(__('messages.t_ap_name_placeholder')),

                TextInput::make('short_name')
                    ->label(__('messages.t_ap_short_name_label'))
                    ->required()
                    ->placeholder(__('messages.t_ap_short_name_placeholder')),

                TextInput::make('start_url')
                    ->label(__('messages.t_ap_start_url_label'))
                    ->required()
                    ->placeholder(__('messages.t_ap_start_url_placeholder')),

                Select::make('display')
                    ->label(__('messages.t_ap_display_label'))
                    ->required()
                    ->options([
                        'fullscreen' => __('messages.t_ap_display_fullscreen'),
                        'standalone' => __('messages.t_ap_display_standalone'),
                        'minimal-ui' => __('messages.t_ap_display_minimal_ui'),
                    ])
                    ->helperText(__('messages.t_ap_display_helper')),

                ColorPicker::make('background_color')
                    ->label(__('messages.t_ap_background_color_label'))
                    ->required()
                    ->placeholder(__('messages.t_ap_background_color_placeholder')),

                ColorPicker::make('theme_color')
                    ->label(__('messages.t_ap_theme_color_label'))
                    ->required()
                    ->placeholder(__('messages.t_ap_theme_color_placeholder')),

                Textarea::make('description')
                    ->label(__('messages.t_ap_description_label'))
                    ->required()
                    ->columnSpanFull()
                    ->placeholder(__('messages.t_ap_description_placeholder')),

                Repeater::make('icons')
                    ->label(__('messages.t_ap_icons_label'))
                    ->collapsible()
                    ->columnSpanFull()
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->itemLabel(fn(array $state): ?string => $state['sizes'] ?? null)
                    ->schema([
                        FileUpload::make('src')
                            ->maxSize(maxUploadFileSize())
                            ->disableLabel()
                            ->disk('media')
                            ->acceptedFileTypes(['image/png'])
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(fn(Get $get) => \Arr::first(explode('x', $get('sizes'))))
                            ->imageResizeTargetHeight(fn(Get $get) => \Arr::last(explode('x', $get('sizes'))))
                            ->helperText(fn(Get $get) => __('messages.t_ap_image_helper_text', ['size' => $get('sizes')])),
                        Hidden::make('sizes')
                    ])
            ]);
    }
}
