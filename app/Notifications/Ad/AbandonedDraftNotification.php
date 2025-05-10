<?php

namespace App\Notifications\Ad;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Ad;

class AbandonedDraftNotification extends Notification
{
    protected $ad;

    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('messages.t_complete_your_ad_draft'))
            ->greeting(__('messages.t_hello', ['name' => $notifiable->name]))
            ->line(__('messages.t_ad_reminder', ['title' => $this->ad->title]))
            ->action(__('messages.t_continue_ad'), route('post-ad', ['id' =>$this->ad->id]))
            ->line(__('messages.t_thank_you'));
    }
}
