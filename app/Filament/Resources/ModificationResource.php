<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdManagement;
use App\Filament\Resources\ModificationResource\Pages;
use App\Filament\Resources\ModificationResource\RelationManagers;
use App\Models\Media;
use Approval\Models\Modification;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class ModificationResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Modification::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $cluster = AdManagement::class;

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_modification');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'view',
            'delete_any',
            'approve',
            'reject'
        ];
    }


    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_modification');
    }

    public static function canView($record): bool
    {
        return userHasPermission('view_modification');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_modification');
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
            ->modifyQueryUsing(fn(Builder $query) => $query->activeOnly())
            ->columns([
                TextColumn::make('ad')
                    ->label(__('messages.t_ap_ad'))
                    ->limit(35)
                    ->default(function ($record) {
                        if ($record->modifiable_type == 'App\Models\Ad') {
                            return $record->modifiable->title ?? '';
                        }
                        if ($record->modifiable_type == 'App\Models\Media') {
                            return $record->modifiable->model->title ?? '';
                        }
                        return '';
                    }),
                TextColumn::make('user')
                    ->label(__('messages.t_ap_user'))
                    ->default(fn($record) => $record->modifier->name ?? ''),
                TextColumn::make('field')
                    ->label(__('messages.t_ap_field'))
                    ->default(function ($record) {
                        if ($record->modifiable_type == 'App\Models\Ad') {
                            $field = array_key_first($record->modifications);
                            $formattedField = explode('_',$field);
                            return head($formattedField) ?? '';
                        }
                        if ($record->modifiable_type == 'App\Models\Media') {
                            return 'image' ?? '';
                        }
                        return '';
                    }),
                TextColumn::make('modified')
                    ->label(__('messages.t_ap_modified'))
                    ->limit(35)
                    ->html()
                    ->default(function ($record): string {
                        return self::getValueFromModifications($record, 'modified');
                    }),
                TextColumn::make('original')
                    ->label(__('messages.t_ap_original'))
                    ->limit(35)
                    ->html()
                    ->default(function ($record): string {
                        return self::getValueFromModifications($record, 'original');
                    }),
                ImageColumn::make('image')
                    ->label(__('messages.t_ap_image'))
                    ->defaultImageUrl(function ($record) {
                        return $record->modifiable_type == 'App\Models\Media' ? $record->modifiable?->getUrl() : '';
                    })
                // ->extraAttributes(fn($record)=>$record->modifiable->getUrl()?['class'=>'hidden']:[])

            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('approve')
                    ->label(__('messages.t_ap_approve'))
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn() => userHasPermission('approve_modification'))
                    ->action(function ($record, $data) {
                        $reason = $data['reason'] ?? '';
                        try {
                            $modification = $record;
                            auth()->user()->approve($record, $reason);

                            if ($modification->modifiable_type == 'App\Models\Ad') {
                                $modificationField = $modification->modifications;
                                if (array_key_exists('title', $modificationField) && $modification->modifiable && $modification->modifiable->title) {
                                    self::updateAdSlug($modification->modifiable, $modification->modifiable->title);
                                }
                            }
                            Notification::make()
                                ->title(__('messages.t_ap_modification_request_rejected'))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('reject')
                    ->label(__('messages.t_ap_reject'))
                    ->color('danger')
                    ->icon('heroicon-m-x-mark')
                    ->form([
                        Textarea::make('reason')
                            ->label(__('messages.t_ap_reason'))
                    ])
                    ->visible(fn() => userHasPermission('reject_modification'))
                    ->action(function ($record, $data) {
                        $reason = $data['reason'] ?? '';
                        try {
                            if ($record->modifiable_type == 'App\Models\Media') {
                                if (isset($data['reason'])) {
                                    $disapprovalModel = config('approval.models.disapproval', \Approval\Models\Disapproval::class);
                                    $disapprovalModel::firstOrCreate([
                                        'disapprover_id' => auth()->id(),
                                        'disapprover_type' => 'App\Models\User',
                                        'modification_id' => $record->id,
                                        'reason' => $reason
                                    ]);
                                }
                                $record->active = false;
                                $record->save();
                            }
                            if ($record->modifiable_type == 'App\Models\Ad') {
                                auth()->user()->disapprove($record, $reason);
                            }
                            Notification::make()
                                ->title(__('messages.t_ap_modification_request_accepted'))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();
                        }

                    }),
                ViewAction::make('view')
                    ->infolist([
                        Grid::make([
                            'default' => 2
                        ])
                            ->schema([
                                TextEntry::make('ad')
                                    ->label(__('messages.t_ap_ad'))
                                    ->default(function ($record) {
                                        if ($record->modifiable_type == 'App\Models\Ad') {
                                            return $record->modifiable->title ?? '';
                                        }
                                        if ($record->modifiable_type == 'App\Models\Media') {
                                            return $record->modifiable->model->title ?? '';
                                        }
                                        return '';
                                    }),
                                TextEntry::make('user')
                                    ->label(__('messages.t_ap_user'))
                                    ->default(fn($record): string => $record->modifier->name ?? ''),
                                TextEntry::make('field')
                                    ->label(__('messages.t_ap_field'))
                                    ->default(function ($record) {
                                        if ($record->modifiable_type == 'App\Models\Ad') {
                                            $field = array_key_first($record->modifications);
                                            $formattedField = explode('_',$field);
                                            return head($formattedField) ?? '';
                                        }
                                        if ($record->modifiable_type == 'App\Models\Media') {
                                            return 'image' ?? '';
                                        }
                                        return '';
                                    }),
                                TextEntry::make('modified')
                                    ->label(__('messages.t_ap_modified'))
                                    ->columnSpanFull()
                                    ->html()
                                    ->default(function ($record): string {
                                        return self::getValueFromModifications($record, 'modified');
                                    }),
                                TextEntry::make('original')
                                    ->label(__('messages.t_ap_original'))
                                    ->columnSpanFull()
                                    ->html()
                                    ->default(function ($record): string {
                                        return self::getValueFromModifications($record, 'original');
                                    }),
                                ImageEntry::make('image')
                                    ->label(__('messages.t_ap_image'))
                                    ->columnSpanFull()
                                    ->defaultImageUrl(function ($record) {
                                        return $record->modifiable_type == 'App\Models\Media' ? $record->modifiable?->getUrl() : '';
                                    })
                            ])
                    ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Retrieve the modified or original value from the record's modifications.
     *
     * @param object $record The record containing modifications.
     * @param string $key The key to retrieve ('modified' or 'original').
     * @return string The formatted modification value or an empty string if not found.
     */
    private static function getValueFromModifications($record, $key)
    {
        //If modification is not from ad then halt the process
        if ($record->modifiable_type !== 'App\Models\Ad') {
            return '';
        }

        $modifications = $record->modifications;
        $firstKey = array_key_first($modifications);

        //If modified key is title then return value
        if ($firstKey === 'title') {
            $flattened = \Arr::dot($record->modifications);
            return $key=='original' ? end($flattened):reset($flattened);
        }

        //If modified description_tiptap is title then convert json array into html
        if ($firstKey === 'description_tiptap') {
            $modification = head($modifications)[$key] ?? '';
            return tiptap_converter()->asHTML($modification) ?: '';
        }

        return '';
    }

    protected static function updateAdSlug($ad, $title)
    {
        $ad->slug = Str::slug(Str::limit($title, 138)) . '-' . substr($ad->id, 0, 8);
        $ad->save();
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
            'index' => Pages\ListModifications::route('/'),
            // 'create' => Pages\CreateModification::route('/create'),
            // 'edit' => Pages\EditModification::route('/{record}/edit'),
        ];
    }
}
