<?php

namespace App\Filament\Resources;

use App\Enums\FieldType;
use App\Enums\FieldValidationType;
use App\Filament\Clusters\DynamicFields;
use App\Filament\Resources\FieldResource\Pages;
use App\Filament\Resources\FieldResource\RelationManagers;
use App\Models\Category;
use App\Models\Field;
use App\Models\FieldGroup;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Forms\Components\Repeater;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FieldResource extends Resource
{
    use Translatable;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = DynamicFields::class;

    protected static ?string $model = Field::class;

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_field');
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_field');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_field');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_field');
    }

    public static function canDelete($record): bool
    {
        return userHasPermission('delete_field');
    }

    public static function fieldSchema()
    {
        return [
            TextInput::make('name')
                ->label(__('messages.t_ap_field_name'))
                ->placeholder(__('messages.t_ap_field_name_placeholder'))
                ->helperText(__('messages.t_ap_field_name_helper'))
                ->required(),

            TextInput::make('helpertext')
                ->label(__('messages.t_ap_field_helpertext'))
                ->helperText(__('messages.t_ap_field_helpertext_description')),

            Select::make('categories')
                ->relationship('categories', 'name', function ($query) {
                    return $query->whereNotNull('parent_id')
                        ->whereHas('parent', function ($parentQuery) {
                            $parentQuery->whereNull('parent_id');
                        });
                })
                ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name}")
                ->preload()
                ->label(__('messages.t_ap_apply_to_subcategories'))
                ->multiple()
                ->helperText(__('messages.t_ap_categories_helper')),

            Select::make('field_group_id')
                ->label(__('messages.t_ap_field_group'))
                ->searchable()
                ->options(FieldGroup::all()->pluck('name', 'id'))
                ->helperText(__('messages.t_ap_field_group_helper')),

            Select::make('type')
                ->label(__('messages.t_ap_field_type'))
                ->live()
                ->required()
                ->enum(FieldType::class)
                ->options(FieldType::class)
                ->helperText(__('messages.t_ap_field_type_helper')),

            Toggle::make('required')
                ->label(__('messages.t_ap_required_field'))
                ->helperText(__('messages.t_ap_required_field_helper')),

            Toggle::make('filterable')
                ->visible(fn($get) => in_array($get('type'), ['text', 'select']))
                ->label(__('messages.t_ap_filterable_field'))
                ->helperText(__('messages.t_ap_filterable_field_helper')),

            Toggle::make('listable')
                ->hidden()
                ->label(__('messages.t_ap_listable_field'))
                ->helperText(__('messages.t_ap_listable_field_helper')),

            Toggle::make('searchable')
                ->label(__('messages.t_ap_searchable_field'))
                ->helperText(__('messages.t_ap_searchable_field_helper')),

            TagsInput::make('options')
                ->label(__('messages.t_ap_field_options'))
                ->visible(fn($get) => in_array($get('type'), ['radio', 'select', 'checkboxlist']))
                ->helperText(__('messages.t_ap_field_options_helper')),

            Select::make('validation_type')
                ->options(FieldValidationType::class)
                ->enum(FieldValidationType::class)
                ->reactive()
                ->helperText(function ($state) {
                    $helperTexts = FieldValidationType::helperTexts();
                    $emptyMessage = __('messages.t_ap_validation_type_empty');
                    return $helperTexts[$state] ?? $emptyMessage;
                })
                ->visible(fn($get) => in_array($get('type'), ['text'])),

            TextInput::make('max_length')
                ->helperText(function (Get $get) {
                    return $get('type') == 'text'
                        ? __('messages.t_ap_max_length_text_helper')
                        : __('messages.t_ap_max_length_number_helper');
                })
                ->visible(fn($get) => in_array($get('type'), ['text', 'number']))
                ->numeric(),

            TextInput::make('min_length')
                ->lt('max_length')
                ->helperText(function (Get $get) {
                    return $get('type') == 'text'
                        ? __('messages.t_ap_min_length_text_helper')
                        : __('messages.t_ap_min_length_number_helper');
                })
                ->visible(fn($get) => in_array($get('type'), ['text', 'number']))
                ->numeric(),

        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::fieldSchema())
            ->columns(2);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->unless(app('filament')->hasPlugin('field-template'), function ($query) {
                $query->whereDoesntHave('fieldTemplateMappings');
            })->orderBy('order', 'asc'))
            ->reorderable('order')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('messages.t_ap_field_name')),

                TextColumn::make('type')
                    ->formatStateUsing(fn($state): string => $state->getLabel())
                    ->badge()
                    ->label(__('messages.t_ap_field_type')),

                TextColumn::make('categories.name')
                    ->label(__('messages.t_ap_category'))
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->expandableLimitedList(),

                TextColumn::make('fieldTemplates.name')
                    ->label(__('messages.t_ap_field_template'))
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->hidden(!app('filament')->hasPlugin('field-template'))
                    ->expandableLimitedList(),

                ToggleColumn::make('required')
                    ->label(__('messages.t_ap_is_required')),


            ])
            ->filters([
                SelectFilter::make('Default Templates')
                    ->label(__('messages.t_ap_default_templates'))
                    ->hidden(!app('filament')->hasPlugin('field-template'))
                    ->relationship('fieldTemplates', 'name', function ($query) {
                        return $query->enabled()->isDefault(true);
                    }),

                SelectFilter::make('Custom Templates')
                    ->label(__('messages.t_ap_custom_templates'))
                    ->hidden(!app('filament')->hasPlugin('field-template'))
                    ->relationship('fieldTemplates', 'name', function ($query) {
                        return $query->enabled()->isDefault(false);
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn($record) => $record->default),
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
            'index' => Pages\ListFields::route('/'),
            'create' => Pages\CreateField::route('/create'),
            'edit' => Pages\EditField::route('/{record}/edit'),
        ];
    }
}
