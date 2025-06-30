<?php

namespace App\Jobs;

use App\Models\CustomerReview;
use App\Models\ReviewModerationAction;
use App\Services\ContentModerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAutomaticModeration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    protected CustomerReview $review;

    public function __construct(CustomerReview $review)
    {
        $this->review = $review;
    }

    public function handle(ContentModerationService $moderationService): void
    {
        try {
            Log::info("Iniciando moderación automática para review ID: {$this->review->id}");

            // Ejecutar análisis de moderación
            $analysis = $moderationService->analyzeContent($this->review);

            // Actualizar score de contenido
            $this->review->update([
                'content_score' => $analysis['score'],
                'auto_moderation_flags' => $analysis['flags']
            ]);

            // Determinar acción basada en el análisis
            $action = $this->determineAction($analysis);

            if ($action) {
                $this->executeAction($action, $analysis);
            }

            Log::info("Moderación automática completada para review ID: {$this->review->id}");

        } catch (\Exception $e) {
            Log::error("Error en moderación automática para review ID: {$this->review->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-lanzar excepción para que Laravel maneje el retry
            throw $e;
        }
    }

    private function determineAction(array $analysis): ?string
    {
        $score = $analysis['score'];
        $flags = $analysis['flags'];

        // Auto-rechazar contenido muy problemático
        if ($score <= 2.0 || in_array('hate_speech', $flags) || in_array('explicit_content', $flags)) {
            return 'auto_reject';
        }

        // Auto-aprobar contenido de alta calidad
        if ($score >= 8.0 && empty($flags)) {
            return 'auto_approve';
        }

        // Flaggear contenido sospechoso
        if ($score <= 5.0 || !empty($flags)) {
            return 'flag_for_review';
        }

        return null; // Sin acción automática
    }

    private function executeAction(string $action, array $analysis): void
    {
        switch ($action) {
            case 'auto_approve':
                $this->autoApprove($analysis);
                break;

            case 'auto_reject':
                $this->autoReject($analysis);
                break;

            case 'flag_for_review':
                $this->flagForReview($analysis);
                break;
        }
    }

    private function autoApprove(array $analysis): void
    {
        $this->review->update([
            'moderation_status' => 'approved',
            'approved_at' => now()
        ]);

        ReviewModerationAction::createAutomatedAction(
            $this->review,
            'auto_approved',
            'high_quality_content',
            'meets_guidelines',
            [
                'content_score' => $analysis['score'],
                'confidence' => $analysis['confidence'] ?? null,
                'processing_time' => microtime(true) - $this->getStartTime()
            ]
        );

        Log::info("Review auto-aprobada: {$this->review->id}", ['score' => $analysis['score']]);
    }

    private function autoReject(array $analysis): void
    {
        $reason = $this->getPrimaryReason($analysis['flags']);

        $this->review->update([
            'moderation_status' => 'rejected',
            'rejected_at' => now(),
            'admin_notes' => 'Rechazado automáticamente por: ' . $reason
        ]);

        ReviewModerationAction::createAutomatedAction(
            $this->review,
            'auto_rejected',
            'content_quality_check',
            $reason,
            [
                'content_score' => $analysis['score'],
                'flags' => $analysis['flags'],
                'confidence' => $analysis['confidence'] ?? null,
                'processing_time' => microtime(true) - $this->getStartTime()
            ]
        );

        Log::warning("Review auto-rechazada: {$this->review->id}", [
            'score' => $analysis['score'],
            'flags' => $analysis['flags']
        ]);
    }

    private function flagForReview(array $analysis): void
    {
        $this->review->update([
            'moderation_status' => 'flagged',
            'admin_notes' => 'Flaggeado automáticamente para revisión manual'
        ]);

        ReviewModerationAction::createAutomatedAction(
            $this->review,
            'flagged',
            'suspicious_content_detector',
            'needs_human_review',
            [
                'content_score' => $analysis['score'],
                'flags' => $analysis['flags'],
                'confidence' => $analysis['confidence'] ?? null,
                'processing_time' => microtime(true) - $this->getStartTime()
            ]
        );

        Log::info("Review flaggeada para revisión: {$this->review->id}", [
            'score' => $analysis['score'],
            'flags' => $analysis['flags']
        ]);
    }

    private function getPrimaryReason(array $flags): string
    {
        $reasonMap = [
            'hate_speech' => 'offensive_language',
            'explicit_content' => 'inappropriate_content',
            'spam_detected' => 'spam',
            'fake_content' => 'fake_review',
            'low_quality' => 'inappropriate_content'
        ];

        foreach ($flags as $flag) {
            if (isset($reasonMap[$flag])) {
                return $reasonMap[$flag];
            }
        }

        return 'inappropriate_content';
    }

    private function getStartTime(): float
    {
        return $this->job->payload()['data']['startTime'] ?? microtime(true);
    }

    public function failed(\Exception $exception): void
    {
        Log::error("Job de moderación automática falló para review ID: {$this->review->id}", [
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Opcional: Notificar a administradores sobre el fallo
        // Notification::route('mail', config('app.admin_email'))
        //     ->notify(new ModerationJobFailedNotification($this->review, $exception));
    }
}