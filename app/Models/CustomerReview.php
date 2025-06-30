<?php

namespace App\Models;

use App\Enums\ReviewModerationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class CustomerReview extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reviewable_id',
        'reviewable_type',
        'rating',
        'feedback',
        'is_verified',
        'user_id',
        'order_id',
        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_notes',
        'helpful_count',
        'not_helpful_count',
        'reported_count',
        'auto_moderation_flags',
        'content_score',
        'approved_at',
        'rejected_at'
    ];

    protected $casts = [
        'auto_moderation_flags' => 'array',
        'content_score' => 'decimal:2',
        'moderated_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_verified' => 'boolean'
    ];

    protected $attributes = [
        'moderation_status' => 'pending',
        'helpful_count' => 0,
        'not_helpful_count' => 0,
        'reported_count' => 0
    ];

    // ==================== RELACIONES ====================

    /**
     * Usuario que hizo la review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Entidad que recibe la review (Ad, User, etc.)
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Moderador que procesó la review
     */
    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Reportes de esta review
     */
    public function reports(): HasMany
    {
        return $this->hasMany(ReviewReport::class, 'review_id');
    }

    /**
     * Acciones de moderación realizadas
     */
    public function moderationActions(): HasMany
    {
        return $this->hasMany(ReviewModerationAction::class, 'review_id');
    }

    /**
     * Respuestas a esta review
     */
    public function responses(): HasMany
    {
        return $this->hasMany(ReviewResponse::class, 'review_id');
    }

    // ==================== SCOPES ====================

    /**
     * Reviews aprobadas
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 'approved');
    }

    /**
     * Reviews pendientes de moderación
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('moderation_status', 'pending');
    }

    /**
     * Reviews rechazadas
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('moderation_status', 'rejected');
    }

    /**
     * Reviews flaggeadas
     */
    public function scopeFlagged(Builder $query): Builder
    {
        return $query->where('moderation_status', 'flagged');
    }

    /**
     * Reviews que necesitan atención (reportadas)
     */
    public function scopeNeedsAttention(Builder $query): Builder
    {
        return $query->where('reported_count', '>', 0)
                    ->orWhere('moderation_status', 'flagged');
    }

    /**
     * Reviews por rating
     */
    public function scopeByRating(Builder $query, int $rating): Builder
    {
        return $query->where('rating', $rating);
    }

    /**
     * Reviews recientes
     */
    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Reviews útiles (con más votos positivos)
     */
    public function scopeHelpful(Builder $query): Builder
    {
        return $query->where('helpful_count', '>', 'not_helpful_count');
    }

    // ==================== MÉTODOS ====================

    /**
     * Aprobar la review
     */
    public function approve(User $moderator, string $notes = null): bool
    {
        $this->update([
            'moderation_status' => 'approved',
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
            'approved_at' => now(),
            'moderation_notes' => $notes
        ]);

        $this->logModerationAction($moderator, 'approved', 'meets_guidelines', $notes);

        return true;
    }

    /**
     * Rechazar la review
     */
    public function reject(User $moderator, string $reason, string $notes = null): bool
    {
        $this->update([
            'moderation_status' => 'rejected',
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
            'rejected_at' => now(),
            'moderation_notes' => $notes
        ]);

        $this->logModerationAction($moderator, 'rejected', $reason, $notes);

        return true;
    }

    /**
     * Flaggear la review para revisión
     */
    public function flag(User $moderator, string $reason, string $notes = null): bool
    {
        $this->update([
            'moderation_status' => 'flagged',
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
            'moderation_notes' => $notes
        ]);

        $this->logModerationAction($moderator, 'flagged', $reason, $notes);

        return true;
    }

    /**
     * Registrar acción de moderación
     */
    private function logModerationAction(User $moderator, string $action, string $reason = null, string $notes = null): void
    {
        ReviewModerationAction::create([
            'review_id' => $this->id,
            'moderator_id' => $moderator->id,
            'action_type' => $action,
            'previous_status' => $this->getOriginal('moderation_status'),
            'new_status' => $this->moderation_status,
            'reason' => $reason,
            'notes' => $notes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Incrementar contador de útil
     */
    public function markAsHelpful(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * Incrementar contador de no útil
     */
    public function markAsNotHelpful(): void
    {
        $this->increment('not_helpful_count');
    }

    /**
     * Incrementar contador de reportes
     */
    public function incrementReportCount(): void
    {
        $this->increment('reported_count');
        
        // Auto-flag si recibe muchos reportes
        if ($this->reported_count >= 3 && $this->moderation_status === 'approved') {
            $this->update(['moderation_status' => 'flagged']);
        }
    }

    /**
     * Calcular score de helpfulness
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
     * Verificar si la review necesita moderación
     */
    public function needsModeration(): bool
    {
        return in_array($this->moderation_status, ['pending', 'flagged']) || $this->reported_count > 0;
    }

    /**
     * Verificar si la review es visible públicamente
     */
    public function isVisible(): bool
    {
        return $this->moderation_status === 'approved';
    }

    /**
     * Obtener el texto del estado de moderación
     */
    public function getModerationStatusTextAttribute(): string
    {
        return match($this->moderation_status) {
            'pending' => 'Pendiente de moderación',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'flagged' => 'Marcada para revisión',
            default => 'Estado desconocido'
        };
    }

    /**
     * Obtener el color del estado para UI
     */
    public function getModerationStatusColorAttribute(): string
    {
        return match($this->moderation_status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'flagged' => 'danger',
            default => 'gray'
        };
    }

    // ==================== MÉTODOS DE RESPUESTAS ====================

    /**
     * Respuestas visibles (activas y aprobadas)
     */
    public function visibleResponses(): HasMany
    {
        return $this->responses()->visible();
    }

    /**
     * Verificar si tiene respuestas
     */
    public function hasResponses(): bool
    {
        return $this->responses()->visible()->exists();
    }

    /**
     * Obtener la respuesta principal (del propietario del anuncio/perfil)
     */
    public function getPrimaryResponse(): ?ReviewResponse
    {
        // Determinar quién puede responder según el tipo de entidad reviewable
        $ownerId = null;
        
        if ($this->reviewable_type === 'App\Models\Ad') {
            $ownerId = $this->reviewable->user_id ?? null;
        } elseif ($this->reviewable_type === 'App\Models\User') {
            $ownerId = $this->reviewable_id;
        }

        if (!$ownerId) {
            return null;
        }

        return $this->responses()
                   ->visible()
                   ->where('user_id', $ownerId)
                   ->first();
    }

    /**
     * Verificar si el usuario puede responder a esta review
     */
    public function canUserRespond(User $user): bool
    {
        // Solo usuarios autenticados pueden responder
        if (!$user->id) {
            return false;
        }

        // No puede responder a su propia review
        if ($this->user_id === $user->id) {
            return false;
        }

        // Solo reviews aprobadas pueden ser respondidas
        if ($this->moderation_status !== 'approved') {
            return false;
        }

        // Verificar si ya respondió
        if ($this->hasUserResponded($user)) {
            return false;
        }

        // Verificar si es el propietario de la entidad reviewada
        return $this->isEntityOwner($user);
    }

    /**
     * Verificar si el usuario ya respondió
     */
    public function hasUserResponded(User $user): bool
    {
        return $this->responses()
                   ->where('user_id', $user->id)
                   ->exists();
    }

    /**
     * Verificar si el usuario es propietario de la entidad reviewada
     */
    public function isEntityOwner(User $user): bool
    {
        if ($this->reviewable_type === 'App\Models\Ad') {
            return $this->reviewable->user_id === $user->id;
        } elseif ($this->reviewable_type === 'App\Models\User') {
            return $this->reviewable_id === $user->id;
        }

        return false;
    }

    /**
     * Crear respuesta a la review
     */
    public function createResponse(User $user, string $responseContent): ?ReviewResponse
    {
        if (!$this->canUserRespond($user)) {
            return null;
        }

        return $this->responses()->create([
            'user_id' => $user->id,
            'response' => $responseContent,
            'status' => 'active',
            'moderation_status' => 'approved' // Las respuestas del propietario se aprueban automáticamente
        ]);
    }

    /**
     * Obtener estadísticas de respuestas
     */
    public function getResponseStatsAttribute(): array
    {
        $responses = $this->responses()->visible();
        
        return [
            'total_responses' => $responses->count(),
            'has_owner_response' => !is_null($this->getPrimaryResponse()),
            'avg_response_time_hours' => $this->getAverageResponseTime(),
            'total_response_interactions' => $responses->sum('helpful_count') + $responses->sum('not_helpful_count')
        ];
    }

    /**
     * Calcular tiempo promedio de respuesta
     */
    private function getAverageResponseTime(): ?float
    {
        $responses = $this->responses()->visible()->get();
        
        if ($responses->isEmpty()) {
            return null;
        }

        $totalHours = $responses->sum(function ($response) {
            return $this->created_at->diffInHours($response->created_at);
        });

        return round($totalHours / $responses->count(), 1);
    }

    /**
     * Obtener estadísticas de la review (extendido)
     */
    public function getStatsAttribute(): array
    {
        return [
            'total_interactions' => $this->helpful_count + $this->not_helpful_count,
            'helpfulness_score' => $this->helpfulness_score,
            'report_count' => $this->reported_count,
            'days_since_created' => $this->created_at->diffInDays(now()),
            'is_controversial' => $this->helpful_count > 0 && $this->not_helpful_count > 0,
            'needs_attention' => $this->needsModeration(),
            'response_stats' => $this->response_stats
        ];
    }

    public function isPending()
    {
        return $this->moderation_status === 'pending';
    }

    public function isApproved()
    {
        return $this->moderation_status === 'approved';
    }

    public function isRejected()
    {
        return $this->moderation_status === 'rejected';
    }
}