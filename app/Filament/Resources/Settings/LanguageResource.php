<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\LanguageResource\Pages;
use App\Filament\Resources\Settings\LanguageResource\RelationManagers;
use App\Models\Country;
use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class LanguageResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Language::class;

    protected static ?int $navigationSort = 13;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'manage'
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_settings::language');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_settings::language');
    }

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_languages');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('messages.t_ap_language_title'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(60)
                    ->hint(__('messages.t_ap_language_title_hint')),

                Forms\Components\TextInput::make('lang_code')
                    ->label(__('messages.t_ap_language_code'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->hint(__('messages.t_ap_language_code_hint')),

                Forms\Components\Select::make('country')
                    ->label(__('messages.t_ap_country_code'))
                    ->required()
                    ->options(Country::all()->pluck('name', 'iso2')),

                Forms\Components\Toggle::make('is_visible')
                    ->helperText(__('messages.t_ap_is_visible_hint')),

                Forms\Components\Toggle::make('rtl')
                    ->helperText(__('messages.t_ap_rtl_hint')),
                Forms\Components\FileUpload::make('icon')
                    ->label(__('messages.t_ap_icon')),
                    // ->hint(__('messages.t_ap_icon_hint')),

            ]);
    }

    public static function table(Table $table): Table
    {
        $isDemo = Config::get('app.demo');
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('messages.t_ap_title'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('lang_code')
                    ->label(__('messages.t_ap_language_code'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('country')
                    ->label(__('messages.t_ap_country'))
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label(__('messages.t_ap_is_visible'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('rtl')
                    ->label(__('messages.t_ap_rtl'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('messages.t_ap_created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('messages.t_ap_updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Manage Translations')
                ->visible(fn () => userHasPermission('manage_settings::language'))
                ->label(__('messages.t_ap_manage_translations'))
                    ->hidden($isDemo)
                    ->icon('heroicon-s-globe-alt')
                    ->url(function ($record) {
                        return 'languages/' . $record->id . '/translate';
                    }),
                Tables\Actions\EditAction::make()->hidden(fn(Language $record): bool => $record->id == 1 || $isDemo),
                Tables\Actions\DeleteAction::make()->hidden(fn(Language $record): bool => $record->lang_code == config('app.locale') || $record->lang_code == config('app.fallback_locale') || $isDemo)
                    ->after(function (Language $record) {
                        // Language directory path
                        $langDir = lang_path(strtolower($record->lang_code));

                        // Check if directory exists
                        if (File::exists($langDir)) {
                            // Remove the directory and its contents
                            File::deleteDirectory($langDir);
                        }

                        // Refresh active languages
                        fetch_active_languages(true);
                    })
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        $isDemo = Config::get('app.demo');

        return $isDemo ? [
            'index' => Pages\ListLanguages::route('/'),
        ] : [
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
            'translate' => Pages\TranslateLanguage::route('/{record}/translate'),
        ];
    }
}
