<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerReviewResource\Pages;
use App\Models\CustomerReview;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CustomerReviewResource extends Resource
{
    protected static ?string $model = CustomerReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Reseñas de Clientes';

    protected static ?string $modelLabel = 'Reseña';

    protected static ?string $pluralModelLabel = 'Reseñas';

    protected static ?string $cluster = \App\Filament\Clusters\FeedbackManagement::class;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Reseña')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('reviewable_type')
                            ->label('Tipo de Entidad')
                            ->options([
                                'App\Models\Ad' => 'Anuncio',
                                'App\Models\User' => 'Usuario',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('reviewable_id')
                            ->label('ID de la Entidad')
                            ->required(),

                        Forms\Components\Select::make('rating')
                            ->label('Calificación')
                            ->options([
                                1 => '1 estrella',
                                2 => '2 estrellas', 
                                3 => '3 estrellas',
                                4 => '4 estrellas',
                                5 => '5 estrellas',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Textarea::make('feedback')
                            ->label('Comentario')
                            ->rows(4)
                            ->required(),

                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificada')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Moderación')
                    ->schema([
                        Forms\Components\Select::make('moderation_status')
                            ->label('Estado de Moderación')
                            ->options([
                                'pending' => 'Pendiente',
                                'approved' => 'Aprobada',
                                'rejected' => 'Rechazada',
                                'flagged' => 'Flaggeada',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('moderated_by')
                            ->label('Moderado por')
                            ->relationship('moderatedBy', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('moderation_notes')
                            ->label('Notas de Moderación')
                            ->rows(3),

                        Forms\Components\TextInput::make('content_score')
                            ->label('Score de Contenido')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(10),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Estadísticas')
                    ->schema([
                        Forms\Components\TextInput::make('helpful_count')
                            ->label('Votos Útiles')
                            ->numeric()
                            ->default(0)
                            ->disabled(),

                        Forms\Components\TextInput::make('not_helpful_count')
                            ->label('Votos No Útiles')
                            ->numeric()
                            ->default(0)
                            ->disabled(),

                        Forms\Components\TextInput::make('reported_count')
                            ->label('Número de Reportes')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1', '2' => 'danger',
                        '3' => 'warning',
                        '4', '5' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => $state . ' ⭐')
                    ->sortable(),

                Tables\Columns\TextColumn::make('feedback')
                    ->label('Comentario')
                    ->limit(50)
                    ->tooltip(function (CustomerReview $record): string {
                        return $record->feedback;
                    }),

                Tables\Columns\TextColumn::make('moderation_status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (CustomerReview $record): string => $record->getModerationStatusColorAttribute())
                    ->formatStateUsing(fn (CustomerReview $record): string => $record->getModerationStatusTextAttribute()),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificada')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('helpful_count')
                    ->label('Útil')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reported_count')
                    ->label('Reportes')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('moderatedBy.name')
                    ->label('Moderado por')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('moderated_at')
                    ->label('Moderada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('moderation_status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                        'flagged' => 'Flaggeada',
                    ])
                    ->multiple(),

                SelectFilter::make('rating')
                    ->label('Calificación')
                    ->options([
                        1 => '1 estrella',
                        2 => '2 estrellas',
                        3 => '3 estrellas', 
                        4 => '4 estrellas',
                        5 => '5 estrellas',
                    ])
                    ->multiple(),

                Filter::make('needs_attention')
                    ->label('Necesita Atención')
                    ->query(fn (Builder $query): Builder => $query->needsAttention())
                    ->toggle(),

                Filter::make('recent')
                    ->label('Recientes (7 días)')
                    ->query(fn (Builder $query): Builder => $query->recent(7))
                    ->toggle(),

                Filter::make('reported')
                    ->label('Reportadas')
                    ->query(fn (Builder $query): Builder => $query->where('reported_count', '>', 0))
                    ->toggle(),

                SelectFilter::make('is_verified')
                    ->label('Verificada')
                    ->options([
                        '1' => 'Sí',
                        '0' => 'No',
                    ]),

                SelectFilter::make('moderated_by')
                    ->label('Moderado por')
                    ->relationship('moderatedBy', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (CustomerReview $record): bool => $record->isPending())
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar Reseña')
                    ->modalDescription('¿Está seguro de que desea aprobar esta reseña?')
                    ->action(function (CustomerReview $record): void {
                        $record->approve(Auth::user(), 'Aprobado desde panel admin');
                        
                        Notification::make()
                            ->title('Reseña aprobada')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (CustomerReview $record): bool => $record->isPending())
                    ->form([
                        Forms\Components\Select::make('reason')
                            ->label('Razón del rechazo')
                            ->options([
                                'inappropriate_content' => 'Contenido inapropiado',
                                'spam' => 'Spam',
                                'fake_review' => 'Reseña falsa',
                                'offensive_language' => 'Lenguaje ofensivo',
                                'other' => 'Otro motivo',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('moderation_notes')
                            ->label('Notas adicionales')
                            ->rows(3),
                    ])
                    ->action(function (CustomerReview $record, array $data): void {
                        $record->reject(Auth::user(), $data['reason'], $data['moderation_notes']);
                        
                        Notification::make()
                            ->title('Reseña rechazada')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('flag')
                    ->label('Marcar')
                    ->icon('heroicon-o-flag')
                    ->color('warning')
                    ->visible(fn (CustomerReview $record): bool => $record->isPending() || $record->isApproved())
                    ->form([
                        Forms\Components\Select::make('reason')
                            ->label('Razón')
                            ->options([
                                'needs_review' => 'Necesita revisión adicional',
                                'suspicious_content' => 'Contenido sospechoso',
                                'multiple_reports' => 'Múltiples reportes',
                                'other' => 'Otro motivo',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('moderation_notes')
                            ->label('Notas')
                            ->rows(3),
                    ])
                    ->action(function (CustomerReview $record, array $data): void {
                        $record->flag(Auth::user(), $data['reason'], $data['moderation_notes']);
                        
                        Notification::make()
                            ->title('Reseña marcada para revisión')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulk_approve')
                        ->label('Aprobar Seleccionadas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aprobar Reseñas')
                        ->modalDescription('¿Está seguro de que desea aprobar todas las reseñas seleccionadas?')
                        ->action(function (Collection $records): void {
                            $moderator = Auth::user();
                            $count = 0;
                            
                            foreach ($records as $record) {
                                if ($record->isPending()) {
                                    $record->approve($moderator, 'Aprobación masiva desde panel admin');
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title("$count reseñas aprobadas exitosamente")
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('bulk_reject')
                        ->label('Rechazar Seleccionadas')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Select::make('reason')
                                ->label('Razón del rechazo')
                                ->options([
                                    'inappropriate_content' => 'Contenido inapropiado',
                                    'spam' => 'Spam',
                                    'fake_review' => 'Reseña falsa',
                                    'offensive_language' => 'Lenguaje ofensivo',
                                    'other' => 'Otro motivo',
                                ])
                                ->required(),
                            Forms\Components\Textarea::make('moderation_notes')
                                ->label('Notas adicionales')
                                ->rows(3),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $moderator = Auth::user();
                            $count = 0;
                            
                            foreach ($records as $record) {
                                if ($record->isPending()) {
                                    $record->reject($moderator, $data['reason'], $data['moderation_notes']);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title("$count reseñas rechazadas exitosamente")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Actualizar cada 30 segundos
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListCustomerReviews::route('/'),
            'create' => Pages\CreateCustomerReview::route('/create'),
            'view' => Pages\ViewCustomerReview::route('/{record}'),
            'edit' => Pages\EditCustomerReview::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'moderatedBy', 'reports']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::pending()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pendingCount = static::getModel()::pending()->count();
        
        if ($pendingCount > 50) {
            return 'danger';
        } elseif ($pendingCount > 20) {
            return 'warning';
        }
        
        return 'primary';
    }
}