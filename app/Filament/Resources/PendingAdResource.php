<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdManagement;
use App\Filament\Resources\PendingAdResource\Pages;
use App\Filament\Resources\PendingAdResource\RelationManagers;
use App\Models\Ad;
use App\Models\PendingAd;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action as ActionsAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class PendingAdResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Ad::class;

    protected static ?string $cluster = AdManagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_ad_pending_ad');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_ad_pending_ad');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'view',
            'add_comment'
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_pending::ad');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->query(Ad::query()->where('status', 'pending'))
            ->defaultPaginationPageOption(5)
            ->defaultSort('posted_date', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('ads')
                    ->collection('ads')
                    ->conversion('thumb')
                    ->defaultImageUrl(fn($record)=>getAdPlaceholderImage($record->id))
                    ->label(__('messages.t_ap_ad_images'))
                    ->size(40)
                    ->circular()
                    ->overlap(2)
                    ->stacked()
                    ->limit(3),

                TextColumn::make('title')
                    ->searchable()
                    ->label(__('messages.t_ap_title')),

                TextColumn::make('user.name')
                    ->label(__('messages.t_ap_posted_by'))
                    ->sortable(),

                TextColumn::make('price')
                    ->label(__('messages.t_ap_price')),

                TextColumn::make('location_name')
                    ->label(__('messages.t_ap_location')),

                TextColumn::make('posted_date')
                    ->label(__('messages.t_ap_posted_on'))
                    ->date(),

                TextColumn::make('category.name')
                    ->label(__('messages.t_ap_category'))
                    ->sortable(),

                SelectColumn::make('status')
                    ->options([
                        'draft' => __('messages.t_ap_status_draft'),
                        'active' => __('messages.t_ap_status_active'),
                        'inactive' => __('messages.t_ap_status_inactive'),
                        'sold' => __('messages.t_ap_status_sold'),
                    ])
                    ->label(__('messages.t_ap_change_status')),

            ])
            ->actions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->visible(fn () => userHasPermission('view_pending::ad'))
                    ->label(__('messages.t_ap_view_details'))
                    ->url(fn(Ad $record): string => route('ad.overview', [
                        'slug' => $record->slug,
                        'admin_view' => 'true'
                    ]))
                    ->openUrlInNewTab(),
                Action::make('add_comment')
                    ->label(__('messages.t_ap_add_comment'))
                    ->visible(fn () => userHasPermission('add_comment_pending::ad'))
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Textarea::make('comment')
                        ->label(__('messages.t_ap_comment'))
                    ])
                    ->fillForm(fn(Ad $record): array => [
                        'comment' => $record->comment,
                    ])
                    ->action(function ($data, $record) {
                        if (isset($data['comment'])) {
                            $record->comment = $data['comment'];
                            $record->save();
                            $user = $record->user;
                            Notification::make()
                                ->title(__('messages.t_pending_ad_notification_title'))
                                ->body($data['comment'])
                                ->actions([
                                    ActionsAction::make(__('messages.t_et_view_ad'))
                                        ->button()
                                        ->url(route('ad.overview', ['slug' => $record->slug]))
                                ])
                                ->sendToDatabase($user);
                        }
                    })
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePendingAds::route('/'),
        ];
    }
}
