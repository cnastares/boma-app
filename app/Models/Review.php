<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'reviewer_id',
        'reviewed_id',
        'reviewer_type',
        'rating',
        'comment',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== RELACIONES ====================

    /**
     * Anuncio al que pertenece la reseña
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Usuario que escribió la reseña
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Usuario que recibe la reseña
     */
    public function reviewed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_id');
    }

    /**
     * Respuesta a la reseña
     */
    public function response(): HasOne
    {
        return $this->hasOne(BidirectionalReviewResponse::class);
    }

    // ==================== SCOPES ====================

    /**
     * Reseñas aprobadas
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /**
     * Reseñas pendientes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Reseñas rechazadas
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Reseñas para un proveedor (escritas por clientes)
     */
    public function scopeForProvider(Builder $query, $userId): Builder
    {
        return $query->where('reviewed_id', $userId)->where('reviewer_type', 'client');
    }

    /**
     * Reseñas para un cliente (escritas por proveedores)
     */
    public function scopeForClient(Builder $query, $userId): Builder
    {
        return $query->where('reviewed_id', $userId)->where('reviewer_type', 'provider');
    }

    /**
     * Reseñas de clientes
     */
    public function scopeFromClients(Builder $query): Builder
    {
        return $query->where('reviewer_type', 'client');
    }

    /**
     * Reseñas de proveedores
     */
    public function scopeFromProviders(Builder $query): Builder
    {
        return $query->where('reviewer_type', 'provider');
    }

    /**
     * Reseñas por rating específico
     */
    public function scopeByRating(Builder $query, int $rating): Builder
    {
        return $query->where('rating', $rating);
    }

    // ==================== MÉTODOS AUXILIARES ====================

    /**
     * Verificar si la reseña puede ser respondida por el usuario
     */
    public function canBeRespondedBy(User $user): bool
    {
        // Solo el usuario reseñado puede responder
        if ($this->reviewed_id !== $user->id) {
            return false;
        }

        // No puede responder si ya hay una respuesta
        if ($this->response) {
            return false;
        }

        // Solo reseñas aprobadas pueden ser respondidas
        return $this->status === 'approved';
    }

    /**
     * Verificar si el usuario puede editar esta reseña
     */
    public function canBeEditedBy(User $user): bool
    {
        // Solo el autor puede editar
        if ($this->reviewer_id !== $user->id) {
            return false;
        }

        // Solo si no tiene respuesta o está pendiente
        return !$this->response && in_array($this->status, ['pending', 'approved']);
    }

    /**
     * Obtener el color del estado para UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'green',
            'pending' => 'yellow',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Obtener el texto del estado
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'approved' => 'Aprobada',
            'pending' => 'Pendiente',
            'rejected' => 'Rechazada',
            default => 'Desconocido'
        };
    }

    /**
     * Obtener el tipo de reviewer en español
     */
    public function getReviewerTypeTextAttribute(): string
    {
        return match($this->reviewer_type) {
            'client' => 'Cliente',
            'provider' => 'Proveedor',
            default => 'Desconocido'
        };
    }

    /**
     * Verificar si es una reseña de cliente
     */
    public function isFromClient(): bool
    {
        return $this->reviewer_type === 'client';
    }

    /**
     * Verificar si es una reseña de proveedor
     */
    public function isFromProvider(): bool
    {
        return $this->reviewer_type === 'provider';
    }

    /**
     * Aprobar la reseña
     */
    public function approve(): bool
    {
        return $this->update(['status' => 'approved']);
    }

    /**
     * Rechazar la reseña
     */
    public function reject(): bool
    {
        return $this->update(['status' => 'rejected']);
    }

    /**
     * Marcar como pendiente
     */
    public function markAsPending(): bool
    {
        return $this->update(['status' => 'pending']);
    }
}