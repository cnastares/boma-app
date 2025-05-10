<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\UserManagement;
use App\Filament\Resources\VerificationCenterResource\Pages;
use App\Filament\Resources\VerificationCenterResource\RelationManagers;
use App\Models\User;
use App\Models\VerificationCenter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\HtmlString;
use Filament\Forms\Get;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class VerificationCenterResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = VerificationCenter::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $cluster = UserManagement::class;

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_verification');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'update',
            'delete_any'
        ];
    }


    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_verification::center');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_verification::center');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_verification::center');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('document_type')
                    ->label(__('messages.t_ap_document_type'))
                    ->required()
                    ->options([
                        'id' => __('messages.t_ap_government_issued_id'),
                        'driver_license' => __('messages.t_ap_driver_license'),
                        'passport' => __('messages.t_ap_passport'),
                    ]),

                Grid::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('front_side')
                            ->label(__('messages.t_ap_document_front_side'))
                            ->maxSize(maxUploadFileSize())
                            ->collection('front_side_verification')
                            ->visibility('private')
                            ->image()
                            ->disabled()
                            ->downloadable(),
                        SpatieMediaLibraryFileUpload::make('back_side')
                            ->label(__('messages.t_ap_document_back_side'))
                            ->maxSize(maxUploadFileSize())
                            ->collection('back_side_verification')
                            ->visibility('private')
                            ->image()
                            ->disabled()
                            ->downloadable(),
                        SpatieMediaLibraryFileUpload::make('download')
                            ->label(__('messages.t_ap_document_selfie_download'))
                            ->maxSize(maxUploadFileSize())
                            ->disabled()
                            ->collection('selfie')
                            ->downloadable(),
                    ]),

                Grid::make()
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => __('messages.t_ap_status_pending'),
                                'declined' => __('messages.t_ap_status_declined'),
                                'verified' => __('messages.t_ap_status_verified'),
                            ])
                            ->label(__('messages.t_ap_document_status')),
                    ]),

                Textarea::make('comments')
                    ->label(__('messages.t_ap_document_comments')),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                ->label(__('messages.t_ap_user')),

            TextColumn::make('document_type')
                ->formatStateUsing(function (string $state): string {
                    switch ($state) {
                        case 'id':
                            return __('messages.t_ap_government_issued_id');
                        case 'driver_license':
                            return __('messages.t_ap_driver_license');
                        case 'passport':
                            return __('messages.t_ap_passport');
                        default:
                            return ucfirst($state);
                    }
                })
                ->label(__('messages.t_ap_document_type')),

            SpatieMediaLibraryImageColumn::make('selfie')
                ->label(__('messages.t_ap_selfie'))
                ->collection('selfie')
                ->defaultImageUrl(asset('images/placeholder.jpg'))
                ->conversion('thumb')
                ->visibility('private')
                ->size(40),

            SpatieMediaLibraryImageColumn::make('front_side')
                ->label(__('messages.t_ap_front_side'))
                ->collection('front_side_verification')
                ->defaultImageUrl(asset('images/placeholder.jpg'))
                ->conversion('thumb')
                ->visibility('private')
                ->size(40),

            SpatieMediaLibraryImageColumn::make('back_side')
                ->label(__('messages.t_ap_back_side'))
                ->collection('back_side_verification')
                ->defaultImageUrl(asset('images/placeholder.jpg'))
                ->conversion('thumb')
                ->visibility('private')
                ->size(40),

            SelectColumn::make('status')
                ->options([
                    'pending' => __('messages.t_ap_status_pending'),
                    'declined' => __('messages.t_ap_status_declined'),
                    'verified' => __('messages.t_ap_status_verified'),
                ])
                    ->afterStateUpdated(function ($record) {
                        if (isset($record->status) && in_array($record->status, ['declined', 'verified']) && isset($record->user_id)) {
                            $recipient = User::find($record->user_id);
                            if ($record->status == 'declined') {
                                $notificationTitle = __('messages.t_verification_rejected_notification_title');
                                $notificationBody = __('messages.t_reason') . $record->comments;
                                //update declined at
                                $record->declined_at = now();
                                $record->save();
                            } elseif ($record->status == 'verified') {
                                $notificationTitle = __('messages.t_verification_verified_notification_title');
                                $notificationBody = __('messages.t_verification_verified_notification_body');
                                //update verified at
                                $record->verified_at = now();
                                $record->save();
                            }
                            if ($recipient) {
                                Notification::make()
                                    ->title($notificationTitle)
                                    ->body($notificationBody)
                                    ->sendToDatabase($recipient);
                            }
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('messages.t_ap_view_ad_details'))
                ,
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
            'index' => Pages\ListVerificationCenters::route('/'),
            'create' => Pages\CreateVerificationCenter::route('/create'),
            'edit' => Pages\EditVerificationCenter::route('/{record}/edit'),
        ];
    }
}
