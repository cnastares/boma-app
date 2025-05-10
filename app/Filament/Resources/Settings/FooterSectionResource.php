<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\FooterSectionResource\Pages;
use App\Filament\Resources\Settings\FooterSectionResource\RelationManagers;
use App\Models\FooterSection;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class FooterSectionResource extends Resource implements HasShieldPermissions
{
    use Translatable;

    protected static ?string $model = FooterSection::class;

    protected static ?int $navigationSort = 4;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'update',
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_settings::footer::section');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_settings::footer::section');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_footer');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }
    public static function getModelLabel(): string
    {
        return __('messages.t_ap_footer_section');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Title of the Footer Section
                TextInput::make('title')
                    ->maxLength(60)
                    ->helperText(__('messages.t_ap_footer_title_helper')),

                // Select Type (Custom or Predefined)
                Select::make('type')
                    ->options([
                        'custom' => __('messages.t_ap_custom'),
                        'predefined' => __('messages.t_ap_predefined'),
                    ])
                    ->reactive()
                    ->required()
                    ->live()
                    ->helperText(__('messages.t_ap_footer_type_helper')),

                // Select Predefined Identifier
                Select::make('predefined_identifier')
                    ->label(__('messages.t_ap_predefined_section'))
                    ->options([
                        'site_with_social' => __('messages.t_ap_site_with_social'),
                        'popular_category' => __('messages.t_ap_popular_category'),
                    ])
                    ->hidden(fn($get) => $get('type') !== 'predefined')
                    ->helperText(__('messages.t_ap_predefined_section_helper')),

                // Select::make('column_span')
                //     ->label('Column Span')
                //     ->options([
                //         1 => '1/12',
                //         2 => '1/6',
                //         3 => '1/4',
                //         4 => '1/3',
                //         6 => '1/2',
                //         8 => '2/3',
                //         9 => '3/4',
                //         12 => 'Full Width'
                //     ])
                //     ->required()
                //     ->helperText('Select the column span for this footer section within a 12-column grid.'),

                // Footer Items Repeater
                Repeater::make('footerItems')
                ->hidden(fn(Get $get) => $get('type') !== 'custom')
                ->relationship()
                ->columnSpanFull()
                ->schema([
                    TextInput::make('name')
                        ->label(__('messages.t_ap_item_name'))
                        ->required()
                        ->helperText(__('messages.t_ap_footer_item_name_helper')),

                    Select::make('type')
                        ->label(__('messages.t_ap_item_type'))
                        ->options([
                            'page' => __('messages.t_ap_page'),
                            'url' => __('messages.t_ap_url'),
                            'predefined' => __('messages.t_ap_predefined')
                        ])
                        ->required()
                        ->reactive()
                        ->helperText(__('messages.t_ap_footer_item_type_helper')),

                    Select::make('predefined_identifier')
                        ->label(__('messages.t_ap_predefined_item'))
                        ->options([
                            'blog' => __('messages.t_ap_blog'),
                            'contact_us' => __('messages.t_ap_contact_us')
                        ])
                        ->hidden(fn ($get) => $get('type') !== 'predefined')
                        ->helperText(__('messages.t_ap_predefined_item_helper')),

                    Select::make('page_id')
                        ->label(__('messages.t_ap_page'))
                        ->required()
                        ->options(Page::visible()->get()->pluck('title', 'id'))
                        ->hidden(fn ($get) => $get('type') !== 'page')
                        ->helperText(__('messages.t_ap_footer_item_page_helper')),

                    TextInput::make('url')
                        ->label(__('messages.t_ap_url'))
                        ->hidden(fn ($get) => $get('type') !== 'url')
                        ->helperText(__('messages.t_ap_footer_item_url_helper'))
                ])
                ->columns(2)
                ->helperText(__('messages.t_ap_footer_items_helper'))
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                        ->label(__('messages.t_ap_title'))
                ,
                Tables\Columns\TextColumn::make('type')
                        ->label(__('messages.t_ap_type'))
                ,
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->orderBy('order'))
            ->reorderable('order')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListFooterSections::route('/'),
            'create' => Pages\CreateFooterSection::route('/create'),
            'edit' => Pages\EditFooterSection::route('/{record}/edit'),
        ];
    }
}
