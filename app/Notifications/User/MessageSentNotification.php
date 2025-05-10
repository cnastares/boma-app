<?php

namespace App\Notifications\User;

use App\Models\Message;
use App\Settings\LiveChatSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class MessageSentNotification extends Notification
{
    use Queueable;

    protected $message, $existingMessagesCount, $buyerName, $productName;

    public function __construct(Message $message, $existingMessagesCount, $buyerName, $productName)
    {
        $this->message = $message;
        $this->existingMessagesCount = $existingMessagesCount;
        $this->buyerName = $buyerName;
        $this->productName = $productName;
    }


    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $notificationTitle = $this->existingMessagesCount == 0 ?
            __('messages.t_new_interest') :
            __('messages.t_new_message');

        $notificationBody = $this->existingMessagesCount == 0 ?
            __('messages.t_received_new_interest', ['buyerName' => $this->buyerName, 'productName' => $this->productName]) :
            __('messages.t_follow_up_message', ['buyerName' => $this->buyerName, 'productName' => $this->productName]);

        // Check if the 'live-chat' plugin is enabled and live chat is enabled
        if (app('filament')->hasPlugin('live-chat') && app(LiveChatSettings::class)->enable_livechat) {
            $url = url("/messages/{$this->message->conversation_id}");
        } else {
            $url = url("/my-messages?conversation_id={$this->message->conversation_id}");
        }

        return (new MailMessage)
                    ->subject($notificationTitle)
                    ->line($notificationBody)
                    ->action('View Message', $url);
    }

   public function toDatabase($notifiable): array
    {
       // Check if the 'live-chat' plugin is enabled and live chat is enabled
        if (app('filament')->hasPlugin('live-chat') && app(LiveChatSettings::class)->enable_livechat) {
            $url = url("/messages/{$this->message->conversation_id}");
        } else {
            $url = url("/my-messages?conversation_id={$this->message->conversation_id}");
        }

        $notificationTitle = $this->existingMessagesCount == 0 ?
            __('messages.t_new_interest') :
            __('messages.t_new_message');

        $notificationBody = $this->existingMessagesCount == 0 ?
            __('messages.t_received_new_interest', ['buyerName' => $this->buyerName, 'productName' => $this->productName]) :
            __('messages.t_follow_up_message', ['buyerName' => $this->buyerName,'renterName' => $this->buyerName, 'productName' => $this->productName,'carName'=>$this->productName]);


        return FilamentNotification::make()
        ->success()
        ->title($notificationTitle)
        ->body($notificationBody)
        ->actions([
            Action::make(__('messages.t_view'))
                ->button()
                ->markAsRead()
                ->url(fn(): string =>  $url)
        ])
        ->getDatabaseMessage();
    }

}
