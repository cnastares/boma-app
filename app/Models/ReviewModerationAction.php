<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ReviewModerationAction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'review_id',
        'moderator_id',
        'action_type',
        'previous_status',
        'new_status',
        'reason',
        'notes',
        'metadata',
        'is_automated',
        'automation_rule',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_automated' => 'boolean'
    ];

    // ==================== RELACIONES ====================

    /**
     * Review afectada
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(CustomerReview::class, 'review_id');
    }

    /**
     * Moderador que realizó la acción
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    // ==================== SCOPES ====================

    /**
     * Acciones manuales
     */
    public function scopeManual(Builder $query): Builder
    {
        return $query->where('is_automated', false);
    }

    /**
     * Acciones automáticas
     */
    public function scopeAutomated(Builder $query): Builder
    {
        return $query->where('is_automated', true);
    }

    /**
     * Acciones por tipo
     */
    public function scopeByActionType(Builder $query, string $actionType): Builder
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Acciones por moderador
     */
    public function scopeByModerator(Builder $query, User $moderator): Builder
    {
        return $query->where('moderator_id', $moderator->id);
    }

    /**
     * Acciones por regla de automatización
     */
    public function scopeByAutomationRule(Builder $query, string $rule): Builder
    {
        return $query->where('automation_rule', $rule);
    }

    /**
     * Acciones recientes
     */
    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hours));
    }

    /**
     * Acciones de aprobación
     */
    public function scopeApprovals(Builder $query): Builder
    {
        return $query->whereIn('action_type', ['approved', 'auto_approved']);
    }

    /**
     * Acciones de rechazo
     */
    public function scopeRejections(Builder $query): Builder
    {
        return $query->whereIn('action_type', ['rejected', 'auto_rejected']);
    }

    // ==================== MÉTODOS ====================

    /**
     * Crear acción de moderación manual
     */
    public static function createManualAction(
        CustomerReview $review,
        User $moderator,
        string $actionType,
        string $reason = null,
        string $notes = null,
        array $metadata = []
    ): self {
        return self::create([
            'review_id' => $review->id,
            'moderator_id' => $moderator->id,
            'action_type' => $actionType,
            'previous_status' => $review->getOriginal('moderation_status'),
            'new_status' => $review->moderation_status,
            'reason' => $reason,
            'notes' => $notes,
            'metadata' => $metadata,
            'is_automated' => false,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Crear acción de moderación automática
     */
    public static function createAutomatedAction(
        CustomerReview $review,
        string $actionType,
        string $automationRule,
        string $reason = null,
        array $metadata = []
    ): self {
        return self::create([
            'review_id' => $review->id,
            'moderator_id' => null, // Sistema automático
            'action_type' => $actionType,
            'previous_status' => $review->getOriginal('moderation_status'),
            'new_status' => $review->moderation_status,
            'reason' => $reason,
            'metadata' => $metadata,
            'is_automated' => true,
            'automation_rule' => $automationRule,
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Obtener el texto de la acción
     */
    public function getActionTextAttribute(): string
    {
        return match($this->action_type) {
            'approved' => 'Aprobada manualmente',
            'rejected' => 'Rechazada manualmente',
            'flagged' => 'Marcada para revisión',
            'hidden' => 'Ocultada',
            'deleted' => 'Eliminada',
            'edited' => 'Editada',
            'restored' => 'Restaurada',
            'escalated' => 'Escalada',
            'auto_approved' => 'Aprobada automáticamente',
            'auto_rejected' => 'Rechazada automáticamente',
            default => 'Acción desconocida'
        };
    }

    /**
     * Obtener el texto de la razón
     */
    public function getReasonTextAttribute(): string
    {
        return match($this->reason) {
            'meets_guidelines' => 'Cumple con las directrices',
            'inappropriate_content' => 'Contenido inapropiado',
            'spam' => 'Spam o contenido no deseado',
            'fake_review' => 'Reseña falsa',
            'offensive_language' => 'Lenguaje ofensivo',
            'harassment' => 'Acoso',
            'misleading_info' => 'Información engañosa',
            'duplicate_content' => 'Contenido duplicado',
            'technical_issue' => 'Problema técnico',
            'user_request' => 'Solicitud del usuario',
            'policy_violation' => 'Violación de políticas',
            'other' => 'Otro motivo',
            default => $this->reason ?? 'Sin motivo especificado'
        };
    }

    /**
     * Obtener el color de la acción para UI
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action_type) {
            'approved', 'auto_approved', 'restored' => 'success',
            'rejected', 'auto_rejected', 'deleted' => 'danger',
            'flagged', 'escalated' => 'warning',
            'hidden', 'edited' => 'info',
            default => 'gray'
        };
    }

    /**
     * Verificar si fue una acción de aprobación
     */
    public function isApproval(): bool
    {
        return in_array($this->action_type, ['approved', 'auto_approved']);
    }

    /**
     * Verificar si fue una acción de rechazo
     */
    public function isRejection(): bool
    {
        return in_array($this->action_type, ['rejected', 'auto_rejected']);
    }

    /**
     * Verificar si fue una acción destructiva
     */
    public function isDestructive(): bool
    {
        return in_array($this->action_type, ['rejected', 'deleted', 'hidden']);
    }

    /**
     * Obtener estadísticas de la acción
     */
    public function getStatsAttribute(): array
    {
        return [
            'minutes_since_created' => $this->created_at->diffInMinutes(now()),
            'hours_since_created' => $this->created_at->diffInHours(now()),
            'is_recent' => $this->created_at->isAfter(now()->subHour()),
            'is_approval' => $this->isApproval(),
            'is_rejection' => $this->isRejection(),
            'is_destructive' => $this->isDestructive(),
            'has_metadata' => !empty($this->metadata),
            'has_notes' => !empty($this->notes)
        ];
    }

    /**
     * Obtener el contexto completo de la acción
     */
    public function getContextAttribute(): array
    {
        return [
            'action' => $this->action_text,
            'reason' => $this->reason_text,
            'moderator' => $this->is_automated ? 'Sistema automático' : $this->moderator?->name,
            'automation_rule' => $this->automation_rule,
            'timestamp' => $this->created_at->format('d/m/Y H:i:s'),
            'ip_address' => $this->ip_address,
            'notes' => $this->notes,
            'metadata' => $this->metadata
        ];
    }
}