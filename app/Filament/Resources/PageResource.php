<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class PageResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Page::class;

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_content_design');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'create',
            'update',
            'view_any',
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_page');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_page');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_page');
    }


    public static function getModelLabel(): string
    {
        return __('messages.t_ap_page');
    }
    public static function form(Form $form): Form
    {
        $recordId = request()->route('record');

        $restrictedIds = [1, 2, 3, 4];
        $shouldHide = in_array($recordId, $restrictedIds);

        return $form
            ->schema([
                TextInput::make('title')
                    ->label(__('messages.t_ap_title'))
                    ->required(),
                MarkdownEditor::make('content')
                    ->label(__('messages.t_ap_content'))
                    ->required(),
                Section::make(__('messages.t_ap_search_engine_preview'))
                    ->description(__('messages.t_ap_search_engine_description'))
                    ->schema([
                        TextInput::make('seo_title')
                            ->label(__('messages.t_ap_seo_title')),
                        TextInput::make('seo_description')
                            ->label(__('messages.t_ap_seo_description')),
                        TextInput::make('slug')
                            ->label(__('messages.t_ap_page_url'))
                            ->unique(ignoreRecord: true)
                            ->disabled($shouldHide)
                            ->required(),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label(__('messages.t_ap_title')),
                TextColumn::make('slug')
                    ->label(__('messages.t_ap_page_url')),
                SelectColumn::make('status')
                    ->options([
                        'visible' => __('messages.t_ap_published'),
                        'hidden' => __('messages.t_ap_draft'),
                    ])
                    ->label(__('messages.t_ap_change_status')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
