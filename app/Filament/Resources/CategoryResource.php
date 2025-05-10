<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\FieldTemplate;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;



class CategoryResource extends Resource implements HasShieldPermissions
{
    use Translatable;

    protected static ?string $model = Category::class;

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_main_category');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_category_management');
    }

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
            'delete_any',
            'delete',
            'force_delete',
            'force_delete_any',
            'restore',
            'restore_any',
        ];
    }
    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_category');
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_category');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_category') && (!$record->isChildCategory());
    }

    public static function canDelete($record): bool
    {
        return userHasPermission('delete_category');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_category');
    }

    public static function canForceDelete($record): bool
    {
        return userHasPermission('force_delete_category');
    }

    public static function canForceDeleteAny(): bool
    {
        return userHasPermission('force_delete_any_category');
    }

    public static function canRestore($record): bool
    {
        return userHasPermission('restore_category');
    }

    public static function canRestoreAny(): bool
    {
        return userHasPermission('restore_any_category');
    }


    public static function form(Form $form): Form
    {
        $fileUpload = SpatieMediaLibraryFileUpload::make('icon')
            ->hidden(function ($record, $operation) {
                return $operation == 'edit' && $record->isSubCategory();
            })
            ->label(__('messages.t_ap_label'))
            ->maxSize(maxUploadFileSize())
            ->collection('category_icons')
            ->visibility('public')
            ->image()
            ->required()
            ->imageEditor();

        $storageType = config('filesystems.default');

        // Optionally set the disk if the storage type is s3
        if ($storageType == 's3') {
            $fileUpload->disk($storageType);
        }

        return $form
            ->schema([
                Select::make('ad_type')
                    ->relationship('adType', 'name')
                    ->required()
                    ->hidden(fn($record, $operation) => self::canHideMainCategoryFields($operation, $record)),
                Forms\Components\TextInput::make('name')
                    ->label(__('messages.t_ap_name'))
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label(__('messages.t_ap_description'))
                    ->maxLength(300)
                    ->required(),
                Forms\Components\TextInput::make('order')
                    ->hidden(fn($record, $operation) => self::canHideMainCategoryFields($operation, $record))
                    ->label(__('messages.t_ap_order'))
                    ->numeric()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->label(__('messages.t_ap_slug'))
                    ->required()
                    ->hintAction(
                        Action::make('generateSlug')
                            ->label(__('messages.t_ap_generate_slug'))
                            ->icon('heroicon-m-bolt')
                            ->action(function (Set $set, Get $get) {
                                if ($get('name')) {
                                    $set('slug', Str::slug($get('name')));
                                }
                            })
                    )
                    ->label(__('messages.t_ap_category_url'))
                    ->unique(ignoreRecord: true),
                $fileUpload,

                Select::make('field_template_id')
                    ->options(FieldTemplate::all()->pluck('name', 'id'))
                    ->searchable()
                    ->hidden(fn($record, $operation) => (!isFieldTemplatePluginEnabled()))
                    ->label(__('messages.t_ap_field_template')),

                SpatieMediaLibraryFileUpload::make('placeholder_image')
                    ->hidden(fn($record, $operation) => self::canHideMainCategoryFields($operation, $record))
                    ->label(__('messages.t_ap_placeholder_image'))
                    ->maxSize(maxUploadFileSize())
                    ->collection('placeholder_images')
                    ->visibility('public')
                    ->image()
                    ->default(getDefaultAdPlaceholderImage())
                    ->imageEditor(),
                ...getVerificationFields(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('adType.name')
                    ->label('Type'),
                TextColumn::make('name')
                    ->label(__('messages.t_ap_main_category')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('parent_id'));
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubcategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Hide main category fields if the operation is 'edit' and the record is a subcategory.
     * @param mixed $operation
     * @param mixed $record
     * @return bool
     */
    public static function canHideMainCategoryFields($operation, $record): bool
    {
        return $operation == 'edit' && $record->isSubCategory();
    }
}
