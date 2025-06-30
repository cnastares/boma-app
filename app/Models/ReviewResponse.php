<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ReviewResponse extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'review_id',
        'user_id',
        'response',
        'status',
        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_notes',
        'helpful_count',
        'not_helpful_count',
        'reported_count',
        'metadata',
        'last_edited_at',
        'edited_by',
        'content_score',
        'auto_flags'
    ];

    protected $casts = [
        'metadata' => 'array',
        'auto_flags' => 'array',
        'moderated_at' => 'datetime',
        'last_edited_at' => 'datetime',
        'content_score' => 'decimal:2'
    ];

    protected $attributes = [
        'status' => 'active',
        'moderation_status' => 'approved',
        'helpful_count' => 0,
        'not_helpful_count' => 0,
        'reported_count' => 0
    ];

    // ==================== RELACIONES ====================

    /**
     * Review a la que se responde
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(CustomerReview::class, 'review_id');
    }

    /**
     * Usuario que responde
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Moderador que procesó la respuesta
     */
    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Usuario que editó por última vez
     */
    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Interacciones con esta respuesta
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(ReviewResponseInteraction::class, 'response_id');
    }

    // ==================== SCOPES ====================

    /**
     * Respuestas activas
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Respuestas ocultas
     */
    public function scopeHidden(Builder $query): Builder
    {
        return $query->where('status', 'hidden');
    }

    /**
     * Respuestas pendientes de moderación
     */
    public function scopePendingModeration(Builder $query): Builder
    {
        return $query->where('moderation_status', 'pending');
    }

    /**
     * Respuestas aprobadas
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 'approved');
    }

    /**
     * Respuestas rechazadas
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('moderation_status', 'rejected');
    }

    /**
     * Respuestas públicamente visibles
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->active()->approved();
    }

    /**
     * Respuestas por usuario
     */
    public function scopeByUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Respuestas recientes
     */
    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Respuestas útiles
     */
    public function scopeHelpful(Builder $query): Builder
    {
        return $query->where('helpful_count', '>', 'not_helpful_count');
    }

    /**
     * Respuestas reportadas
     */
    public function scopeReported(Builder $query): Builder
    {
        return $query->where('reported_count', '>', 0);
    }

    /**
     * Respuestas editadas
     */
    public function scopeEdited(Builder $query): Builder
    {
        return $query->whereNotNull('last_edited_at');
    }

    // ==================== MÉTODOS DE MODERACIÓN ====================

    /**
     * Aprobar la respuesta
     */
    public function approve(User $moderator, string $notes = null): bool
    {
        $success = $this->update([
            'moderation_status' => 'approved',
            'status' => 'active',
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
            'moderation_notes' => $notes
        ]);

        if ($success) {
            $this->logModerationAction($moderator, 'approved', $notes);
        }

        return $success;
    }

    /**
     * Rechazar la respuesta
     */
    public function reject(User $moderator, string $reason, string $notes = null): bool
    {
        $success = $this->update([
            'moderation_status' => 'rejected',
            'status' => 'hidden',
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
            'moderation_notes' => $notes
        ]);

        if ($success) {
            $this->logModerationAction($moderator, 'rejected', $notes, ['reason' => $reason]);
        }

        return $success;
    }

    /**
     * Ocultar la respuesta
     */
    public function hide(User $moderator = null, string $reason = null): bool
    {
        $success = $this->update([
            'status' => 'hidden',
            'moderated_by' => $moderator?->id,
            'moderated_at' => $moderator ? now() : null,
            'moderation_notes' => $reason
        ]);

        if ($success && $moderator) {
            $this->logModerationAction($moderator, 'hidden', $reason);
        }

        return $success;
    }

    /**
     * Restaurar respuesta oculta
     */
    public function restore(User $moderator, string $notes = null): bool
    {
        $success = $this->update([
            'status' => 'active',
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
            'moderation_notes' => $notes
        ]);

        if ($success) {
            $this->logModerationAction($moderator, 'restored', $notes);
        }

        return $success;
    }

    /**
     * Registrar acción de moderación
     */
    private function logModerationAction(User $moderator, string $action, string $notes = null, array $metadata = []): void
    {
        // Implementar logging si se necesita historial detallado
        // Esto podría ser una tabla separada o agregarlo a la tabla existente de moderation_actions
    }

    // ==================== MÉTODOS DE INTERACCIÓN ====================

    /**
     * Marcar como útil
     */
    public function markAsHelpful(User $user): bool
    {
        // Verificar si ya interactuó
        $existing = $this->interactions()
                        ->where('user_id', $user->id)
                        ->where('interaction_type', 'helpful')
                        ->first();

        if ($existing) {
            return false; // Ya marcó como útil
        }

        // Remover interacción "not_helpful" si existe
        $this->interactions()
             ->where('user_id', $user->id)
             ->where('interaction_type', 'not_helpful')
             ->delete();

        // Crear nueva interacción
        $interaction = $this->interactions()->create([
            'user_id' => $user->id,
            'interaction_type' => 'helpful',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Actualizar contadores
        $this->recalculateHelpfulnessCounts();

        // Enviar notificación al autor de la respuesta
        $this->notifyResponseAuthor($interaction);

        return true;
    }

    /**
     * Marcar como no útil
     */
    public function markAsNotHelpful(User $user): bool
    {
        // Verificar si ya interactuó
        $existing = $this->interactions()
                        ->where('user_id', $user->id)
                        ->where('interaction_type', 'not_helpful')
                        ->first();

        if ($existing) {
            return false; // Ya marcó como no útil
        }

        // Remover interacción "helpful" si existe
        $this->interactions()
             ->where('user_id', $user->id)
             ->where('interaction_type', 'helpful')
             ->delete();

        // Crear nueva interacción
        $interaction = $this->interactions()->create([
            'user_id' => $user->id,
            'interaction_type' => 'not_helpful',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Actualizar contadores
        $this->recalculateHelpfulnessCounts();

        // Enviar notificación al autor de la respuesta (solo para reportes)
        $this->notifyResponseAuthor($interaction);

        return true;
    }

    /**
     * Reportar respuesta
     */
    public function report(User $user, string $reason, string $description = null): bool
    {
        // Verificar si ya reportó
        $existing = $this->interactions()
                        ->where('user_id', $user->id)
                        ->where('interaction_type', 'report')
                        ->first();

        if ($existing) {
            return false; // Ya reportó
        }

        // Crear reporte
        $interaction = $this->interactions()->create([
            'user_id' => $user->id,
            'interaction_type' => 'report',
            'interaction_data' => [
                'reason' => $reason,
                'description' => $description
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Incrementar contador de reportes
        $this->increment('reported_count');

        // Auto-ocultar si recibe muchos reportes
        if ($this->reported_count >= 3 && $this->status === 'active') {
            $this->hide(null, 'Auto-ocultado por múltiples reportes');
        }

        // Enviar notificación al autor de la respuesta
        $this->notifyResponseAuthor($interaction);

        return true;
    }

    /**
     * Recalcular contadores de utilidad
     */
    private function recalculateHelpfulnessCounts(): void
    {
        $helpful = $this->interactions()->where('interaction_type', 'helpful')->count();
        $notHelpful = $this->interactions()->where('interaction_type', 'not_helpful')->count();

        $this->update([
            'helpful_count' => $helpful,
            'not_helpful_count' => $notHelpful
        ]);
    }

    // ==================== MÉTODOS DE EDICIÓN ====================

    /**
     * Editar respuesta
     */
    public function editContent(User $user, string $newContent): bool
    {
        // Verificar permisos (solo el autor puede editar)
        if ($this->user_id !== $user->id) {
            return false;
        }

        // Guardar contenido anterior en metadatos
        $previousContent = $this->response;
        $metadata = $this->metadata ?? [];
        $metadata['edit_history'] = $metadata['edit_history'] ?? [];
        $metadata['edit_history'][] = [
            'previous_content' => $previousContent,
            'edited_at' => now()->toISOString(),
            'edited_by' => $user->id
        ];

        return $this->update([
            'response' => $newContent,
            'last_edited_at' => now(),
            'edited_by' => $user->id,
            'metadata' => $metadata
        ]);
    }

    /**
     * Verificar si fue editada
     */
    public function isEdited(): bool
    {
        return !is_null($this->last_edited_at);
    }

    /**
     * Obtener número de ediciones
     */
    public function getEditCountAttribute(): int
    {
        return count($this->metadata['edit_history'] ?? []);
    }

    // ==================== ATRIBUTOS CALCULADOS ====================

    /**
     * Verificar si es visible públicamente
     */
    public function isVisible(): bool
    {
        return $this->status === 'active' && $this->moderation_status === 'approved';
    }

    /**
     * Verificar si necesita moderación
     */
    public function needsModeration(): bool
    {
        return $this->moderation_status === 'pending' || 
               $this->reported_count >= 3 ||
               ($this->auto_flags && count($this->auto_flags) > 0);
    }

    /**
     * Calcular score de utilidad
     */
    public function getHelpfulnessScoreAttribute(): float
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        
        if ($total === 0) {
            return 0;
        }

        return round(($this->helpful_count / $total) * 100, 1);
    }

    /**
     * Obtener estadísticas de la respuesta
     */
    public function getStatsAttribute(): array
    {
        return [
            'total_interactions' => $this->helpful_count + $this->not_helpful_count,
            'helpfulness_score' => $this->helpfulness_score,
            'report_count' => $this->reported_count,
            'edit_count' => $this->edit_count,
            'days_since_created' => $this->created_at->diffInDays(now()),
            'is_edited' => $this->isEdited(),
            'is_visible' => $this->isVisible(),
            'needs_moderation' => $this->needsModeration()
        ];
    }

    /**
     * Verificar si el usuario puede interactuar
     */
    public function canUserInteract(User $user): bool
    {
        // No puede interactuar con su propia respuesta
        if ($this->user_id === $user->id) {
            return false;
        }

        // No puede interactuar si la respuesta no es visible
        if (!$this->isVisible()) {
            return false;
        }

        return true;
    }

    /**
     * Verificar si el usuario puede editar
     */
    public function canUserEdit(User $user): bool
    {
        // Solo el autor puede editar
        if ($this->user_id !== $user->id) {
            return false;
        }

        // No puede editar si está oculta o rechazada
        if (in_array($this->status, ['hidden', 'deleted'])) {
            return false;
        }

        // Límite de tiempo para editar (24 horas)
        if ($this->created_at->addDay()->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Verificar si el usuario puede eliminar
     */
    public function canUserDelete(User $user): bool
    {
        // Solo el autor o admin puede eliminar
        return $this->user_id === $user->id || $user->is_admin;
    }

    /**
     * Enviar notificación al autor de la respuesta sobre una interacción
     */
    private function notifyResponseAuthor(ReviewResponseInteraction $interaction): void
    {
        try {
            \App\Jobs\Review\SendInteractionNotificationJob::dispatch($this, $interaction);
            
            \Log::info('Job de notificación de interacción encolado', [
                'response_id' => $this->id,
                'interaction_id' => $interaction->id,
                'interaction_type' => $interaction->interaction_type
            ]);
        } catch (\Exception $e) {
            \Log::warning('Error encolando notificación de interacción', [
                'response_id' => $this->id,
                'interaction_id' => $interaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}