<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\StoreBannerResource\Pages;
use App\Filament\App\Resources\StoreBannerResource\RelationManagers;
use App\Models\StoreBanner;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreBannerResource extends Resource
{
    protected static ?string $model = StoreBanner::class;
    protected static ?int $navigationSort = 8;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_banner');
    }

    public static function getModelLabel(): string
    {
        return __('messages.t_banner');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('messages.t_insights_navigation');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('banner_image')
                    ->maxSize(maxUploadFileSize())
                    ->label(__('messages.t_user_banner_image'))
                    ->collection('user_banner_images')
                    ->responsiveImages()
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth('1480')
                    ->imageResizeTargetHeight('350')
                    ->required()
                    ->helperText(__('messages.t_user_banner_image_helpertext')),
                TextInput::make('alternative_text')
                    ->label(__('messages.t_alternative_text')),
                TextInput::make('link')
                    ->label(__('messages.t_link'))
                    ->url(),
                Hidden::make('user_id')
                    ->default(auth()->id())
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyState(view('tables.empty-state', ['message' => __('messages.t_no_banners')]))
            ->modifyQueryUsing(fn($query) => $query->where('user_id', auth()->id()))
            ->columns([
                SpatieMediaLibraryImageColumn::make('banner_image')
                    ->label(__('messages.t_user_banner_image'))
                    ->collection('user_banner_images')
                    ->extraImgAttributes(fn ($record): array => [
                        'alt' => $record->name . __('messages.t_banner_image'),
                    ]),
                TextColumn::make('alternative_text')
                ->label(__('messages.t_alternative_text')),
                TextColumn::make('clicks')
                    ->label(__('messages.t_clicks'))
                    ->visible(function () {
                        return (getSubscriptionSetting('status') && getActiveSubscriptionPlan() &&  getActiveSubscriptionPlan()->product_engagement_level == 'advanced');
                    }),
                TextColumn::make('views')
                    ->label(__('messages.t_views'))
                    ->visible(function () {
                        return (getSubscriptionSetting('status') && getActiveSubscriptionPlan() &&  in_array(getActiveSubscriptionPlan()->product_engagement_level, ['basic', 'advanced']));
                    }),

            ])
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

    public static function canAccess(): bool
    {
        return getSubscriptionSetting('status') && getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->banner_count;
    }
    public static function canCreate(): bool
    {

        $bannerLimit = getActiveSubscriptionPlan() ? getActiveSubscriptionPlan()->banner_count : 0;
        $activeBanners = StoreBanner::where('user_id', auth()->id())->count();
        return $bannerLimit > $activeBanners;
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStoreBanners::route('/'),
            'create' => Pages\CreateStoreBanner::route('/create'),
            'edit' => Pages\EditStoreBanner::route('/{record}/edit'),
        ];
    }
}
