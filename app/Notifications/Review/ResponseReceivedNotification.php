<?php

namespace App\Notifications\Review;

use App\Models\CustomerReview;
use App\Models\ReviewResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class ResponseReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public CustomerReview $review;
    public ReviewResponse $response;

    /**
     * Crear nueva instancia de notificación
     */
    public function __construct(CustomerReview $review, ReviewResponse $response)
    {
        $this->review = $review;
        $this->response = $response;
    }

    /**
     * Canales de notificación
     */
    public function via($notifiable): array
    {
        $channels = ['database'];
        
        // Agregar email si el usuario tiene habilitadas las notificaciones por email
        if ($notifiable->email_notifications ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Notificación por email
     */
    public function toMail($notifiable): MailMessage
    {
        $entityName = $this->getEntityName();
        $entityUrl = $this->getEntityUrl();
        
        return (new MailMessage)
                    ->subject('Nueva respuesta a tu reseña')
                    ->greeting('¡Hola ' . $notifiable->name . '!')
                    ->line('Has recibido una respuesta a tu reseña sobre ' . $entityName . '.')
                    ->line('**Tu reseña:** "' . Str::limit($this->review->feedback, 150) . '"')
                    ->line('**Respuesta:** "' . Str::limit($this->response->response, 200) . '"')
                    ->action('Ver respuesta completa', $entityUrl)
                    ->line('Puedes responder o marcar la respuesta como útil si te ha sido de ayuda.')
                    ->line('Gracias por contribuir a nuestra comunidad.');
    }

    /**
     * Notificación en base de datos
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'review_response_received',
            'title' => 'Nueva respuesta a tu reseña',
            'message' => $this->response->user->name . ' respondió a tu reseña sobre ' . $this->getEntityName(),
            'data' => [
                'review_id' => $this->review->id,
                'response_id' => $this->response->id,
                'reviewer_name' => $this->response->user->name,
                'reviewer_avatar' => $this->response->user->profile_image,
                'entity_type' => $this->review->reviewable_type,
                'entity_id' => $this->review->reviewable_id,
                'entity_name' => $this->getEntityName(),
                'entity_url' => $this->getEntityUrl(),
                'response_preview' => Str::limit($this->response->response, 100),
                'created_at' => $this->response->created_at
            ],
            'read_at' => null
        ];
    }

    /**
     * Obtener nombre de la entidad reviewada
     */
    private function getEntityName(): string
    {
        $reviewable = $this->review->reviewable;
        
        if ($reviewable instanceof \App\Models\Ad) {
            return $reviewable->title ?? 'Anuncio';
        } elseif ($reviewable instanceof \App\Models\User) {
            return $reviewable->name ?? 'Perfil de usuario';
        }
        
        return 'Elemento';
    }

    /**
     * Obtener URL de la entidad
     */
    private function getEntityUrl(): string
    {
        $reviewable = $this->review->reviewable;
        
        if ($reviewable instanceof \App\Models\Ad) {
            return route('ad.show', $reviewable->slug ?? $reviewable->id);
        } elseif ($reviewable instanceof \App\Models\User) {
            return route('user.profile', $reviewable->username ?? $reviewable->id);
        }
        
        return route('home');
    }

    /**
     * ID único de la notificación
     */
    public function uniqueId(): string
    {
        return 'review_response_' . $this->review->id . '_' . $this->response->id;
    }
}