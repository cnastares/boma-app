<?php

namespace App\Filament\Resources\CustomerReviewResource\Pages;

use App\Filament\Resources\CustomerReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewCustomerReview extends ViewRecord
{
    protected static string $resource = CustomerReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Reseña')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Usuario'),
                        
                        Infolists\Components\TextEntry::make('rating')
                            ->label('Calificación')
                            ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state) . " ($state/5)"),
                        
                        Infolists\Components\TextEntry::make('feedback')
                            ->label('Comentario')
                            ->columnSpanFull(),
                        
                        Infolists\Components\IconEntry::make('is_verified')
                            ->label('Verificada')
                            ->boolean(),
                        
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Estado de Moderación')
                    ->schema([
                        Infolists\Components\TextEntry::make('moderation_status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'flagged' => 'primary',
                                default => 'gray',
                            }),
                        
                        Infolists\Components\TextEntry::make('moderatedBy.name')
                            ->label('Moderado por')
                            ->placeholder('Sin moderar'),
                        
                        Infolists\Components\TextEntry::make('moderated_at')
                            ->label('Fecha de Moderación')
                            ->dateTime()
                            ->placeholder('Sin moderar'),
                        
                        Infolists\Components\TextEntry::make('admin_notes')
                            ->label('Notas del Administrador')
                            ->columnSpanFull()
                            ->placeholder('Sin notas'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Estadísticas')
                    ->schema([
                        Infolists\Components\TextEntry::make('helpful_count')
                            ->label('Votos Útiles')
                            ->badge()
                            ->color('success'),
                        
                        Infolists\Components\TextEntry::make('not_helpful_count')
                            ->label('Votos No Útiles')
                            ->badge()
                            ->color('danger'),
                        
                        Infolists\Components\TextEntry::make('reported_count')
                            ->label('Reportes')
                            ->badge()
                            ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success'),
                        
                        Infolists\Components\TextEntry::make('helpfulness_score')
                            ->label('Score de Utilidad')
                            ->formatStateUsing(fn (float $state): string => number_format($state, 1) . '%')
                            ->badge()
                            ->color(fn (float $state): string => match (true) {
                                $state >= 80 => 'success',
                                $state >= 60 => 'warning',
                                default => 'danger',
                            }),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Metadatos')
                    ->schema([
                        Infolists\Components\TextEntry::make('content_score')
                            ->label('Score de Contenido')
                            ->placeholder('Sin evaluar'),
                        
                        Infolists\Components\TextEntry::make('auto_moderation_flags')
                            ->label('Flags Automáticos')
                            ->formatStateUsing(fn (?array $state): string => 
                                $state ? implode(', ', $state) : 'Ninguno')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}