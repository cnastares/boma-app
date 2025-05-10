<?php

namespace App\Filament\Clusters\AdPlacements\Resources;

use App\Filament\Clusters\AdPlacements;
use App\Filament\Clusters\AdPlacements\Resources\CategoryAdPlacementResource\Pages;
use App\Filament\Clusters\AdPlacements\Resources\CategoryAdPlacementResource\RelationManagers;
use App\Models\Category;
use App\Models\CategoryAdPlacement;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class CategoryAdPlacementResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CategoryAdPlacement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = AdPlacements::class;

    protected static ?int $navigationSort =2;

    public static function getPermissionPrefixes(): array
    {
        return [
            'create',
            'update',
            'view_any',
            'delete_any',
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_category::ad::placement');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_category::ad::placement');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_category::ad::placement');
    }
    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_category::ad::placement');
    }


    public static function getModelLabel(): string
    {
        return __('messages.t_ap_category_ad_placement');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ad_type')
                ->options([
                    'script'=>__('messages.t_ap_ad_script'),
                    'images'=>__('messages.t_ap_images'),
                ])
                ->required()
                ->default('script')
                ->reactive()
                ->helperText(__('messages.t_ap_ad_type_helper'))
                ->label(__('messages.t_ap_ad_type')),
                Repeater::make('images')
                    ->required()
                    ->visible(fn(Get $get) => $get('ad_type') == 'images')
                    ->defaultItems(1)
                    ->columnSpanFull()
                    ->helperText(__('messages.t_ap_images_helper'))
                    ->label(__('messages.t_ap_images'))
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label(__('messages.t_ap_image'))
                            ->maxSize(maxUploadFileSize())
                            ->helperText(__('messages.t_ap_image_helper'))
                            ->image()
                            ->required()
                            ->imageEditor(),
                        Forms\Components\TextInput::make('link')
                            ->label(__('messages.t_ap_link'))
                            ->url()
                            ->helperText(__('messages.t_ap_link_helper'))
                            ,
                        Forms\Components\TextInput::make('alt')
                            ->helperText(__('messages.t_ap_alt_text_helper'))
                            ->label(__('messages.t_ap_alt_text'))
                    ]),
                Forms\Components\Textarea::make('value')
                    ->visible(fn(Get $get) => is_null($get('ad_type')) || $get('ad_type') == 'script')
                    ->columnSpanFull()
                    ->required()
                    ->label(__('messages.t_ap_ad_script'))
                    ->helperText(__('messages.t_ap_ad_script_helper'))
                    ->placeholder(__('messages.t_ap_ad_script_placeholder')),

                Forms\Components\Select::make('priority')
                    ->options([
                        'main' => __('messages.t_ap_main_category'),
                        'sub' => __('messages.t_ap_subcategory')
                    ])
                    ->required()
                    ->default('main')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        // If priority is main, clear subcategory
                        if ($state === 'main') {
                            $set('subcategory_id', null);
                        }
                        // If priority is sub, require subcategory
                        if ($state === 'sub') {
                            $set('category_id', null);
                        }
                    })
                    ->label(__('messages.t_ap_priority')),

                Forms\Components\Select::make('category_id')
                    ->searchable()
                    ->options(Category::whereNull('parent_id')->pluck('name', 'id'))
                    ->required()
                    ->hidden(fn(Get $get) => $get('priority') == 'sub')
                    ->reactive()
                    ->helperText(__('messages.t_ap_main_category_helper'))
                    ->label(__('messages.t_ap_main_category')),

                Forms\Components\Select::make('subcategory_id')
                    ->helperText(__('messages.t_ap_subcategory_helper'))
                    ->visible(fn(Get $get) => $get('priority') == 'sub')
                    ->searchable()
                    ->options(
                        Category::whereNotNull('parent_id')
                        ->whereHas('parent', function ($parentQuery) {
                        $parentQuery->whereNull('parent_id');
                    })
                    ->pluck('name', 'id'))
                    ->label(__('messages.t_ap_subcategory'))
                    ->required()
                    ->reactive(),

                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label(__('messages.t_ap_active')),

            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('priority')
                    ->label(__('messages.t_ap_priority'))
                    ->formatStateUsing(function ($state) {
                        $options = [
                            'main' => __('messages.t_ap_main_category'),
                            'sub' => __('messages.t_ap_subcategory')
                        ];
                        return $options[$state];
                    }),
                TextColumn::make('category_name')
                    ->label(__('messages.t_ap_category'))
                    ->default(function ($record) {
                        if ($record->priority == 'main') {
                            return $record->category->name;
                        } else {
                            return $record->subcategory->name;
                        }
                    }),
                TextColumn::make('value')
                    ->label(__('messages.t_ap_ad_script'))
                    ->limit(15),
                IconColumn::make('is_active')
                    ->label(__('messages.t_ap_active'))
                    ->boolean(),

            ])
            ->heading(__('messages.t_ap_table_heading_helper'). ' ' . __('messages.t_display_filter_not_enabled_in_map_view_popup'))
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListCategoryAdPlacements::route('/'),
            'create' => Pages\CreateCategoryAdPlacement::route('/create'),
            'edit' => Pages\EditCategoryAdPlacement::route('/{record}/edit'),
        ];
    }
}
