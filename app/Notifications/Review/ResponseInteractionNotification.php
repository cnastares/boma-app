<?php

namespace App\Notifications\Review;

use App\Models\ReviewResponse;
use App\Models\ReviewResponseInteraction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ResponseInteractionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ReviewResponse $response;
    public ReviewResponseInteraction $interaction;

    /**
     * Crear nueva instancia de notificación
     */
    public function __construct(ReviewResponse $response, ReviewResponseInteraction $interaction)
    {
        $this->response = $response;
        $this->interaction = $interaction;
    }

    /**
     * Canales de notificación
     */
    public function via($notifiable): array
    {
        $channels = ['database'];
        
        // Solo notificar por email para reportes o interacciones importantes
        if ($this->interaction->interaction_type === 'report' && ($notifiable->email_notifications ?? true)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Notificación por email (solo para reportes)
     */
    public function toMail($notifiable): MailMessage
    {
        if ($this->interaction->interaction_type !== 'report') {
            return null;
        }

        return (new MailMessage)
                    ->subject('Tu respuesta ha sido reportada')
                    ->greeting('Hola ' . $notifiable->name)
                    ->line('Tu respuesta a una reseña ha sido reportada por otro usuario.')
                    ->line('**Tu respuesta:** "' . Str::limit($this->response->response, 200) . '"')
                    ->line('**Motivo del reporte:** ' . $this->getReportReasonText())
                    ->line('Nuestro equipo de moderación revisará el contenido.')
                    ->line('Si consideras que el reporte es injustificado, puedes contactar con soporte.')
                    ->action('Ver mis respuestas', route('user.responses'))
                    ->line('Recuerda mantener un tono respetuoso en todas tus interacciones.');
    }

    /**
     * Notificación en base de datos
     */
    public function toDatabase($notifiable): array
    {
        $data = [
            'type' => 'response_interaction',
            'data' => [
                'response_id' => $this->response->id,
                'interaction_id' => $this->interaction->id,
                'interaction_type' => $this->interaction->interaction_type,
                'interactor_name' => $this->interaction->user->name,
                'interactor_avatar' => $this->interaction->user->profile_image,
                'response_preview' => Str::limit($this->response->response, 100),
                'review_entity_name' => $this->getEntityName(),
                'created_at' => $this->interaction->created_at
            ],
            'read_at' => null
        ];

        switch ($this->interaction->interaction_type) {
            case 'helpful':
                $data['title'] = 'Tu respuesta fue marcada como útil';
                $data['message'] = $this->interaction->user->name . ' marcó tu respuesta como útil';
                break;
                
            case 'not_helpful':
                $data['title'] = 'Tu respuesta fue marcada como no útil';
                $data['message'] = $this->interaction->user->name . ' marcó tu respuesta como no útil';
                break;
                
            case 'report':
                $data['title'] = 'Tu respuesta fue reportada';
                $data['message'] = 'Tu respuesta fue reportada por ' . $this->getReportReasonText();
                $data['data']['report_reason'] = $this->interaction->report_reason;
                $data['data']['report_description'] = $this->interaction->report_description;
                break;
                
            default:
                $data['title'] = 'Nueva interacción en tu respuesta';
                $data['message'] = 'Hay una nueva interacción en tu respuesta';
        }

        return $data;
    }

    /**
     * Obtener texto del motivo del reporte
     */
    private function getReportReasonText(): string
    {
        return match($this->interaction->report_reason) {
            'spam' => 'Spam',
            'inappropriate' => 'Contenido inapropiado',
            'harassment' => 'Acoso',
            'false_info' => 'Información falsa',
            'other' => 'Otro motivo',
            default => 'Motivo no especificado'
        };
    }

    /**
     * Obtener nombre de la entidad reviewada
     */
    private function getEntityName(): string
    {
        $reviewable = $this->response->review->reviewable;
        
        if ($reviewable instanceof \App\Models\Ad) {
            return $reviewable->title ?? 'Anuncio';
        } elseif ($reviewable instanceof \App\Models\User) {
            return $reviewable->name ?? 'Perfil de usuario';
        }
        
        return 'Elemento';
    }

    /**
     * ID único de la notificación
     */
    public function uniqueId(): string
    {
        return 'response_interaction_' . $this->response->id . '_' . $this->interaction->id;
    }
}