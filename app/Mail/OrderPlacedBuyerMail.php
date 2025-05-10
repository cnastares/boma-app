<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlacedBuyerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $subject;
    public $line;
    public $histories;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $subject, $line)
    {
        $this->order = $order;
        $this->subject = $subject;
        $this->line = $line;
        $this->histories = $this->order->histories()->whereNotNull('action_date')->get();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
