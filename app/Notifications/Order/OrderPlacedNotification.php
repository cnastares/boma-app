<?php

namespace App\Notifications\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public $order;
    public $subject;
    public $histories;
    /**
     * Create a new notification instance.
     */
    public function __construct($order, $subject)
    {
        $this->order = $order;
        $this->subject = $subject;
        $this->histories = $this->order->histories()->whereNotNull('action_date')->get();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.order-updated', [
                'order' => $this->order,
                'histories' => $this->histories
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
        ->title(function(){
                $adTitle = $this->order->items->first()?->ad->title ?? '';
                return __('messages.t_et_order_status') . " : $adTitle";
        })
        ->body($this->subject)
        ->actions([
            Action::make('go_to_seller_dashboard')
                ->label(__('messages.t_go_to_seller_dashboard'))
                ->button()
                // ->hidden(fn () => request()->is('dashboard/*'))
                ->url(route('filament.app.home'))
        ])
        ->getDatabaseMessage();
    }
}
