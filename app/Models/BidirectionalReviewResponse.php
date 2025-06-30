<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BidirectionalReviewResponse extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'bidirectional_review_responses';

    protected $fillable = [
        'review_id',
        'responder_id',
        'response_text',
        'client_rating'
    ];

    protected $casts = [
        'review_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== RELACIONES ====================

    /**
     * Reseña a la que pertenece esta respuesta
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Usuario que escribió la respuesta
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    // ==================== MÉTODOS AUXILIARES ====================

    /**
     * Verificar si la respuesta puede ser editada por el usuario
     */
    public function canBeEditedBy(User $user): bool
    {
        // Solo el autor puede editar
        return $this->responder_id === $user->id;
    }

    /**
     * Verificar si tiene calificación para el cliente
     */
    public function hasClientRating(): bool
    {
        return !is_null($this->client_rating);
    }

    /**
     * Obtener el texto de la calificación del cliente
     */
    public function getClientRatingTextAttribute(): string
    {
        if (!$this->client_rating) {
            return 'Sin calificación';
        }

        return match($this->client_rating) {
            1 => 'Muy malo',
            2 => 'Malo',
            3 => 'Regular',
            4 => 'Bueno',
            5 => 'Excelente',
            default => 'Sin calificación'
        };
    }

    /**
     * Verificar si la respuesta es de un proveedor hacia un cliente
     */
    public function isProviderResponse(): bool
    {
        return $this->review && $this->review->reviewer_type === 'client';
    }

    /**
     * Verificar si la respuesta es de un cliente hacia un proveedor
     */
    public function isClientResponse(): bool
    {
        return $this->review && $this->review->reviewer_type === 'provider';
    }

    /**
     * Actualizar la respuesta
     */
    public function updateResponse(string $responseText, ?int $clientRating = null): bool
    {
        $data = ['response_text' => $responseText];
        
        if (!is_null($clientRating)) {
            $data['client_rating'] = $clientRating;
        }

        return $this->update($data);
    }
}