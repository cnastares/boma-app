<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrendAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $alertType;
    protected string $alertMessage;

    /**
     * Create a new notification instance.
     *
     * @param string $alertType
     * @param string $alertMessage
     */
    public function __construct(string $alertType, string $alertMessage)
    {
        $this->alertType = $alertType;
        $this->alertMessage = $alertMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('messages.t_trend_alert_subject', ['type' => $this->alertType]))
            ->line(__('messages.t_trend_alert_greeting', ['name' => $notifiable->name]))
            ->line($this->alertMessage)
            ->line(__('messages.t_trend_alert_thank_you'));
    }
}
