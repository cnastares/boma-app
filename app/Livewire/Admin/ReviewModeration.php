<?php

namespace App\Livewire\Admin;

use App\Models\CustomerReview;
use App\Models\ReviewReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ReviewModeration extends Component
{
    use WithPagination;

    // Propiedades de filtros
    public string $statusFilter = 'all';
    public string $priorityFilter = 'all';
    public string $moderatorFilter = 'all';
    public string $searchTerm = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 20;

    // Modal de moderación
    public bool $showModerationModal = false;
    public ?CustomerReview $selectedReview = null;
    public string $moderationAction = '';
    public string $moderationReason = '';
    public string $moderationNotes = '';

    // Modal de reporte
    public bool $showReportModal = false;
    public ?ReviewReport $selectedReport = null;
    public string $reportResolution = '';
    public string $reportNotes = '';

    // Estadísticas
    public array $stats = [];

    protected $rules = [
        'moderationAction' => 'required|string|in:approve,reject,flag',
        'moderationReason' => 'required|string',
        'moderationNotes' => 'nullable|string|max:1000',
        'reportResolution' => 'required|string',
        'reportNotes' => 'nullable|string|max:500'
    ];

    protected $messages = [
        'moderationAction.required' => 'Debe seleccionar una acción de moderación.',
        'moderationReason.required' => 'Debe especificar una razón.',
        'reportResolution.required' => 'Debe seleccionar una acción para el reporte.',
    ];

    public function mount()
    {
        $this->loadStats();
    }

    public function render()
    {
        $reviews = $this->getReviews();
        $reports = $this->getReports();
        $moderators = $this->getModerators();

        return view('livewire.admin.review-moderation', [
            'reviews' => $reviews,
            'reports' => $reports,
            'moderators' => $moderators,
            'reasonOptions' => $this->getReasonOptions(),
            'resolutionOptions' => $this->getResolutionOptions()
        ]);
    }

    public function getReviews(): LengthAwarePaginator
    {
        return CustomerReview::query()
            ->with(['user', 'reviewable', 'moderatedBy', 'reports'])
            ->when($this->statusFilter !== 'all', function (Builder $query) {
                if ($this->statusFilter === 'needs_attention') {
                    $query->needsAttention();
                } else {
                    $query->where('moderation_status', $this->statusFilter);
                }
            })
            ->when($this->moderatorFilter !== 'all', function (Builder $query) {
                if ($this->moderatorFilter === 'unassigned') {
                    $query->whereNull('moderated_by');
                } else {
                    $query->where('moderated_by', $this->moderatorFilter);
                }
            })
            ->when($this->searchTerm, function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    $subQuery->where('feedback', 'like', '%' . $this->searchTerm . '%')
                            ->orWhereHas('user', function (Builder $userQuery) {
                                $userQuery->where('name', 'like', '%' . $this->searchTerm . '%')
                                         ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                            });
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getReports(): LengthAwarePaginator
    {
        return ReviewReport::query()
            ->with(['review.user', 'reporter', 'assignedTo'])
            ->when($this->priorityFilter !== 'all', function (Builder $query) {
                $query->where('priority', $this->priorityFilter);
            })
            ->when($this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getModerators(): array
    {
        return User::where('is_admin', true)
                  ->orWhereHas('roles', function (Builder $query) {
                      $query->where('name', 'moderator');
                  })
                  ->get(['id', 'name'])
                  ->toArray();
    }

    public function loadStats(): void
    {
        $this->stats = [
            'pending_reviews' => CustomerReview::pending()->count(),
            'flagged_reviews' => CustomerReview::flagged()->count(),
            'pending_reports' => ReviewReport::pending()->count(),
            'urgent_reports' => ReviewReport::where('priority', 'urgent')->count(),
            'today_approved' => CustomerReview::approved()
                                            ->whereDate('approved_at', today())
                                            ->count(),
            'today_rejected' => CustomerReview::rejected()
                                            ->whereDate('rejected_at', today())
                                            ->count(),
            'average_resolution_time' => $this->getAverageResolutionTime(),
            'backlog_age' => $this->getBacklogAge()
        ];
    }

    private function getAverageResolutionTime(): float
    {
        $resolvedReports = ReviewReport::resolved()
                                     ->whereNotNull('resolved_at')
                                     ->whereDate('resolved_at', '>=', now()->subDays(7))
                                     ->get();

        if ($resolvedReports->isEmpty()) {
            return 0;
        }

        $totalHours = $resolvedReports->sum(function ($report) {
            return $report->created_at->diffInHours($report->resolved_at);
        });

        return round($totalHours / $resolvedReports->count(), 1);
    }

    private function getBacklogAge(): int
    {
        $oldestPending = CustomerReview::pending()
                                     ->orWhere('moderation_status', 'flagged')
                                     ->oldest()
                                     ->first();

        return $oldestPending ? $oldestPending->created_at->diffInDays(now()) : 0;
    }

    // ==================== ACCIONES DE MODERACIÓN ====================

    public function openModerationModal(CustomerReview $review): void
    {
        $this->selectedReview = $review;
        $this->moderationAction = '';
        $this->moderationReason = '';
        $this->moderationNotes = '';
        $this->showModerationModal = true;
    }

    public function closeModerationModal(): void
    {
        $this->showModerationModal = false;
        $this->selectedReview = null;
        $this->resetValidation();
    }

    public function moderateReview(): void
    {
        $this->validate([
            'moderationAction' => 'required|string|in:approve,reject,flag',
            'moderationReason' => 'required|string',
            'moderationNotes' => 'nullable|string|max:1000'
        ]);

        $moderator = Auth::user();
        
        try {
            switch ($this->moderationAction) {
                case 'approve':
                    $this->selectedReview->approve($moderator, $this->moderationNotes);
                    $message = 'Reseña aprobada exitosamente.';
                    break;

                case 'reject':
                    $this->selectedReview->reject($moderator, $this->moderationReason, $this->moderationNotes);
                    $message = 'Reseña rechazada exitosamente.';
                    break;

                case 'flag':
                    $this->selectedReview->flag($moderator, $this->moderationReason, $this->moderationNotes);
                    $message = 'Reseña marcada para revisión adicional.';
                    break;
            }

            $this->dispatch('review-moderated', message: $message);
            $this->closeModerationModal();
            $this->loadStats();
            
        } catch (\Exception $e) {
            $this->dispatch('moderation-error', message: 'Error al moderar la reseña: ' . $e->getMessage());
        }
    }

    public function bulkApprove(array $reviewIds): void
    {
        $moderator = Auth::user();
        $count = 0;

        foreach ($reviewIds as $reviewId) {
            $review = CustomerReview::find($reviewId);
            if ($review && $review->moderation_status === 'pending') {
                $review->approve($moderator, 'Aprobación masiva');
                $count++;
            }
        }

        $this->dispatch('bulk-action-completed', message: "{$count} reseñas aprobadas exitosamente.");
        $this->loadStats();
    }

    public function bulkReject(array $reviewIds, string $reason = 'inappropriate_content'): void
    {
        $moderator = Auth::user();
        $count = 0;

        foreach ($reviewIds as $reviewId) {
            $review = CustomerReview::find($reviewId);
            if ($review && $review->moderation_status === 'pending') {
                $review->reject($moderator, $reason, 'Rechazo masivo');
                $count++;
            }
        }

        $this->dispatch('bulk-action-completed', message: "{$count} reseñas rechazadas exitosamente.");
        $this->loadStats();
    }

    // ==================== ACCIONES DE REPORTES ====================

    public function openReportModal(ReviewReport $report): void
    {
        $this->selectedReport = $report;
        $this->reportResolution = '';
        $this->reportNotes = '';
        $this->showReportModal = true;
    }

    public function closeReportModal(): void
    {
        $this->showReportModal = false;
        $this->selectedReport = null;
        $this->resetValidation();
    }

    public function resolveReport(): void
    {
        $this->validate([
            'reportResolution' => 'required|string',
            'reportNotes' => 'nullable|string|max:500'
        ]);

        try {
            $this->selectedReport->resolve($this->reportResolution, $this->reportNotes);
            
            $this->dispatch('report-resolved', message: 'Reporte resuelto exitosamente.');
            $this->closeReportModal();
            $this->loadStats();
            
        } catch (\Exception $e) {
            $this->dispatch('report-error', message: 'Error al resolver el reporte: ' . $e->getMessage());
        }
    }

    public function dismissReport(): void
    {
        try {
            $this->selectedReport->dismiss($this->reportNotes);
            
            $this->dispatch('report-dismissed', message: 'Reporte descartado exitosamente.');
            $this->closeReportModal();
            $this->loadStats();
            
        } catch (\Exception $e) {
            $this->dispatch('report-error', message: 'Error al descartar el reporte: ' . $e->getMessage());
        }
    }

    public function assignReport(ReviewReport $report, string $moderatorId): void
    {
        $moderator = User::find($moderatorId);
        
        if ($moderator && $report->assignTo($moderator)) {
            $this->dispatch('report-assigned', message: "Reporte asignado a {$moderator->name}.");
            $this->loadStats();
        } else {
            $this->dispatch('assignment-error', message: 'Error al asignar el reporte.');
        }
    }

    // ==================== FILTROS Y ORDENAMIENTO ====================

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->loadStats();
    }

    public function updatedPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function updatedModeratorFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearchTerm(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->statusFilter = 'all';
        $this->priorityFilter = 'all';
        $this->moderatorFilter = 'all';
        $this->searchTerm = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
        $this->loadStats();
    }

    // ==================== OPCIONES PARA SELECTS ====================

    private function getReasonOptions(): array
    {
        return [
            'meets_guidelines' => 'Cumple con las directrices',
            'inappropriate_content' => 'Contenido inapropiado',
            'spam' => 'Spam o contenido no deseado',
            'fake_review' => 'Reseña falsa',
            'offensive_language' => 'Lenguaje ofensivo',
            'harassment' => 'Acoso',
            'misleading_info' => 'Información engañosa',
            'duplicate_content' => 'Contenido duplicado',
            'policy_violation' => 'Violación de políticas',
            'other' => 'Otro motivo'
        ];
    }

    private function getResolutionOptions(): array
    {
        return [
            'no_action' => 'No se requiere acción',
            'warning_sent' => 'Enviar advertencia al usuario',
            'review_hidden' => 'Ocultar la reseña',
            'review_deleted' => 'Eliminar la reseña',
            'user_suspended' => 'Suspender usuario temporalmente',
            'user_banned' => 'Banear usuario permanentemente'
        ];
    }
}