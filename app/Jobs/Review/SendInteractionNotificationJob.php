<?php

namespace App\Jobs\Review;

use App\Models\ReviewResponse;
use App\Models\ReviewResponseInteraction;
use App\Notifications\Review\ResponseInteractionNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendInteractionNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ReviewResponse $response;
    public ReviewResponseInteraction $interaction;

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
    public function __construct(ReviewResponse $response, ReviewResponseInteraction $interaction)
    {
        $this->response = $response;
        $this->interaction = $interaction;
    }

    /**
     * Ejecutar el job
     */
    public function handle(): void
    {
        try {
            // Verificar que la response e interaction aún existen
            if (!$this->response->exists || !$this->interaction->exists) {
                Log::warning('Response o Interaction eliminados antes de enviar notificación', [
                    'response_id' => $this->response->id,
                    'interaction_id' => $this->interaction->id
                ]);
                return;
            }

            // Obtener el autor de la respuesta
            $responseAuthor = $this->response->user;
            
            if (!$responseAuthor) {
                Log::warning('Usuario autor de respuesta no encontrado', [
                    'response_id' => $this->response->id,
                    'user_id' => $this->response->user_id
                ]);
                return;
            }

            // No notificar si el autor de la interacción es el mismo que el de la respuesta
            if ($responseAuthor->id === $this->interaction->user_id) {
                return;
            }

            // Solo notificar para ciertos tipos de interacciones
            if (!$this->shouldNotifyForInteractionType($this->interaction->interaction_type)) {
                return;
            }

            // Verificar si el usuario tiene las notificaciones habilitadas
            if (!$this->shouldSendNotification($responseAuthor)) {
                Log::info('Notificación de interacción omitida por preferencias del usuario', [
                    'user_id' => $responseAuthor->id,
                    'interaction_id' => $this->interaction->id,
                    'interaction_type' => $this->interaction->interaction_type
                ]);
                return;
            }

            // Enviar notificación
            $responseAuthor->notify(new ResponseInteractionNotification($this->response, $this->interaction));

            Log::info('Notificación de interacción enviada exitosamente', [
                'response_id' => $this->response->id,
                'interaction_id' => $this->interaction->id,
                'interaction_type' => $this->interaction->interaction_type,
                'recipient_id' => $responseAuthor->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando notificación de interacción', [
                'response_id' => $this->response->id,
                'interaction_id' => $this->interaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-lanzar la excepción para que el job sea reintentado
            throw $e;
        }
    }

    /**
     * Determinar si se debe notificar para este tipo de interacción
     */
    private function shouldNotifyForInteractionType(string $interactionType): bool
    {
        return match($interactionType) {
            'helpful' => true,      // Notificar cuando marcan como útil
            'not_helpful' => false, // No notificar cuando marcan como no útil (puede ser molesto)
            'report' => true,       // Siempre notificar reportes
            default => false
        };
    }

    /**
     * Verificar si se debe enviar la notificación
     */
    private function shouldSendNotification($user): bool
    {
        // Verificar si el usuario tiene las notificaciones de interacciones habilitadas
        if (isset($user->notification_preferences) && 
            !($user->notification_preferences['response_interactions'] ?? true)) {
            return false;
        }

        // Verificar si el usuario no está suspendido o bloqueado
        if ($user->is_suspended ?? false) {
            return false;
        }

        // Para reportes, siempre notificar (es importante)
        if ($this->interaction->interaction_type === 'report') {
            return true;
        }

        // Para otras interacciones, verificar configuración más específica
        if (isset($user->notification_preferences) && 
            !($user->notification_preferences['helpful_votes'] ?? true)) {
            return false;
        }

        return true;
    }

    /**
     * Manejar el fallo del job
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de notificación de interacción falló definitivamente', [
            'response_id' => $this->response->id,
            'interaction_id' => $this->interaction->id,
            'interaction_type' => $this->interaction->interaction_type,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}