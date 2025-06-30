<?php

namespace App\Jobs\Review;

use App\Models\CustomerReview;
use App\Models\ReviewResponse;
use App\Notifications\Review\ResponseReceivedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendResponseNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public CustomerReview $review;
    public ReviewResponse $response;

    /**
     * Número de intentos permitidos
     */
    public $tries = 3;

    /**
     * Tiempo de expiración del job (en segundos)
     */
    public $timeout = 60;

    /**
     * Crear nueva instancia del job
     */
    public function __construct(CustomerReview $review, ReviewResponse $response)
    {
        $this->review = $review;
        $this->response = $response;
    }

    /**
     * Ejecutar el job
     */
    public function handle(): void
    {
        try {
            // Verificar que la review y response aún existen
            if (!$this->review->exists || !$this->response->exists) {
                Log::warning('Review o Response eliminados antes de enviar notificación', [
                    'review_id' => $this->review->id,
                    'response_id' => $this->response->id
                ]);
                return;
            }

            // Obtener el autor de la review
            $reviewAuthor = $this->review->user;
            
            if (!$reviewAuthor) {
                Log::warning('Usuario autor de review no encontrado', [
                    'review_id' => $this->review->id,
                    'user_id' => $this->review->user_id
                ]);
                return;
            }

            // No notificar si el autor de la respuesta es el mismo que el de la review
            if ($reviewAuthor->id === $this->response->user_id) {
                return;
            }

            // Verificar si el usuario tiene las notificaciones habilitadas
            if (!$this->shouldSendNotification($reviewAuthor)) {
                Log::info('Notificación omitida por preferencias del usuario', [
                    'user_id' => $reviewAuthor->id,
                    'response_id' => $this->response->id
                ]);
                return;
            }

            // Enviar notificación
            $reviewAuthor->notify(new ResponseReceivedNotification($this->review, $this->response));

            Log::info('Notificación de respuesta enviada exitosamente', [
                'review_id' => $this->review->id,
                'response_id' => $this->response->id,
                'recipient_id' => $reviewAuthor->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando notificación de respuesta', [
                'review_id' => $this->review->id,
                'response_id' => $this->response->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-lanzar la excepción para que el job sea reintentado
            throw $e;
        }
    }

    /**
     * Verificar si se debe enviar la notificación
     */
    private function shouldSendNotification($user): bool
    {
        // Verificar si el usuario tiene las notificaciones globales habilitadas
        if (isset($user->notification_preferences) && 
            !($user->notification_preferences['review_responses'] ?? true)) {
            return false;
        }

        // Verificar si el usuario no está suspendido o bloqueado
        if ($user->is_suspended ?? false) {
            return false;
        }

        // Verificar que el email del usuario sea válido
        if (!$user->email || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    /**
     * Manejar el fallo del job
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de notificación de respuesta falló definitivamente', [
            'review_id' => $this->review->id,
            'response_id' => $this->response->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}