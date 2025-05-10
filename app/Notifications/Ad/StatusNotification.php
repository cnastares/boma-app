<?php

namespace App\Notifications\Ad;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusNotification extends Notification
{
    use Queueable;

    public $ad;
    public $subject;
    public $line;
    /**
     * Create a new notification instance.
     */
    public function __construct($ad, $subject, $line)
    {
        $this->ad = $ad;
        $this->subject = $subject;
        $this->line = $line;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting(__('messages.t_et_ad_status_greeting', ['name' => $notifiable->name]))
            ->subject($this->subject)
            ->line($this->line)
            ->action(__('messages.t_et_view_ad'), route('ad.overview', ['slug' => $this->ad->slug]));
    }


    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->title($this->subject)
            ->body($this->line)
            ->actions([
                Action::make(__('messages.t_et_view_ad'))
                    ->button()
                    ->url(route('ad.overview',['slug'=>$this->ad->slug]))
            ])
            ->getDatabaseMessage();
    }
}
