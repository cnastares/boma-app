<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ReviewResponseInteraction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'response_id',
        'user_id',
        'interaction_type',
        'interaction_data',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'interaction_data' => 'array'
    ];

    // ==================== RELACIONES ====================

    /**
     * Respuesta relacionada
     */
    public function response(): BelongsTo
    {
        return $this->belongsTo(ReviewResponse::class, 'response_id');
    }

    /**
     * Usuario que interactúa
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==================== SCOPES ====================

    /**
     * Interacciones de tipo helpful
     */
    public function scopeHelpful(Builder $query): Builder
    {
        return $query->where('interaction_type', 'helpful');
    }

    /**
     * Interacciones de tipo not_helpful
     */
    public function scopeNotHelpful(Builder $query): Builder
    {
        return $query->where('interaction_type', 'not_helpful');
    }

    /**
     * Reportes
     */
    public function scopeReports(Builder $query): Builder
    {
        return $query->where('interaction_type', 'report');
    }

    /**
     * Interacciones por usuario
     */
    public function scopeByUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Interacciones por tipo
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('interaction_type', $type);
    }

    // ==================== MÉTODOS ====================

    /**
     * Verificar si es un reporte
     */
    public function isReport(): bool
    {
        return $this->interaction_type === 'report';
    }

    /**
     * Verificar si es una valoración positiva
     */
    public function isPositive(): bool
    {
        return $this->interaction_type === 'helpful';
    }

    /**
     * Verificar si es una valoración negativa
     */
    public function isNegative(): bool
    {
        return $this->interaction_type === 'not_helpful';
    }

    /**
     * Obtener la razón del reporte (si aplica)
     */
    public function getReportReasonAttribute(): ?string
    {
        if (!$this->isReport()) {
            return null;
        }

        return $this->interaction_data['reason'] ?? null;
    }

    /**
     * Obtener la descripción del reporte (si aplica)
     */
    public function getReportDescriptionAttribute(): ?string
    {
        if (!$this->isReport()) {
            return null;
        }

        return $this->interaction_data['description'] ?? null;
    }
}