<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ReviewReport extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'review_id',
        'reported_by',
        'reason',
        'description',
        'status',
        'priority',
        'assigned_to',
        'resolution_notes',
        'resolution_action',
        'assigned_at',
        'resolved_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'pending',
        'priority' => 'medium'
    ];

    // ==================== RELACIONES ====================

    /**
     * Review reportada
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(CustomerReview::class, 'review_id');
    }

    /**
     * Usuario que reportó
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Moderador asignado
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ==================== SCOPES ====================

    /**
     * Reportes pendientes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Reportes en revisión
     */
    public function scopeUnderReview(Builder $query): Builder
    {
        return $query->where('status', 'under_review');
    }

    /**
     * Reportes resueltos
     */
    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Reportes por prioridad
     */
    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Reportes urgentes
     */
    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Reportes asignados a un moderador
     */
    public function scopeAssignedTo(Builder $query, User $moderator): Builder
    {
        return $query->where('assigned_to', $moderator->id);
    }

    /**
     * Reportes sin asignar
     */
    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_to');
    }

    /**
     * Reportes por tipo de razón
     */
    public function scopeByReason(Builder $query, string $reason): Builder
    {
        return $query->where('reason', $reason);
    }

    /**
     * Reportes antiguos (más de X días)
     */
    public function scopeOlderThan(Builder $query, int $days): Builder
    {
        return $query->where('created_at', '<', Carbon::now()->subDays($days));
    }

    // ==================== MÉTODOS ====================

    /**
     * Asignar reporte a un moderador
     */
    public function assignTo(User $moderator): bool
    {
        return $this->update([
            'assigned_to' => $moderator->id,
            'assigned_at' => now(),
            'status' => 'under_review'
        ]);
    }

    /**
     * Resolver el reporte
     */
    public function resolve(string $action, string $notes = null): bool
    {
        $success = $this->update([
            'status' => 'resolved',
            'resolution_action' => $action,
            'resolution_notes' => $notes,
            'resolved_at' => now()
        ]);

        if ($success && $action !== 'no_action') {
            $this->review->incrementReportCount();
        }

        return $success;
    }

    /**
     * Descartar el reporte
     */
    public function dismiss(string $notes = null): bool
    {
        return $this->update([
            'status' => 'dismissed',
            'resolution_notes' => $notes,
            'resolved_at' => now()
        ]);
    }

    /**
     * Escalar el reporte
     */
    public function escalate(string $notes = null): bool
    {
        return $this->update([
            'status' => 'escalated',
            'priority' => 'urgent',
            'resolution_notes' => $notes
        ]);
    }

    /**
     * Verificar si el reporte está vencido
     */
    public function isOverdue(): bool
    {
        $slaHours = match($this->priority) {
            'urgent' => 2,
            'high' => 8,
            'medium' => 24,
            'low' => 72,
            default => 24
        };

        return $this->created_at->addHours($slaHours)->isPast() && 
               in_array($this->status, ['pending', 'under_review']);
    }

    /**
     * Calcular tiempo de resolución
     */
    public function getResolutionTimeAttribute(): ?int
    {
        if (!$this->resolved_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->resolved_at);
    }

    /**
     * Obtener el texto de la razón
     */
    public function getReasonTextAttribute(): string
    {
        return match($this->reason) {
            'spam' => 'Spam o contenido no deseado',
            'inappropriate_content' => 'Contenido inapropiado',
            'fake_review' => 'Reseña falsa o fraudulenta',
            'offensive_language' => 'Lenguaje ofensivo',
            'harassment' => 'Acoso o intimidación',
            'misleading_information' => 'Información engañosa',
            'off_topic' => 'Fuera de tema',
            'duplicate' => 'Contenido duplicado',
            'other' => 'Otro motivo',
            default => 'Motivo no especificado'
        };
    }

    /**
     * Obtener el texto del estado
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pendiente',
            'under_review' => 'En revisión',
            'resolved' => 'Resuelto',
            'dismissed' => 'Descartado',
            'escalated' => 'Escalado',
            default => 'Estado desconocido'
        };
    }

    /**
     * Obtener el color del estado para UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'under_review' => 'info',
            'resolved' => 'success',
            'dismissed' => 'gray',
            'escalated' => 'danger',
            default => 'gray'
        };
    }

    /**
     * Obtener el color de la prioridad para UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'gray',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    /**
     * Obtener estadísticas del reporte
     */
    public function getStatsAttribute(): array
    {
        return [
            'days_since_created' => $this->created_at->diffInDays(now()),
            'hours_since_created' => $this->created_at->diffInHours(now()),
            'is_overdue' => $this->isOverdue(),
            'resolution_time' => $this->resolution_time,
            'has_been_assigned' => !is_null($this->assigned_to),
            'is_resolved' => in_array($this->status, ['resolved', 'dismissed'])
        ];
    }
}