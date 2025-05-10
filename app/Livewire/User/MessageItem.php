<?php

namespace App\Livewire\User;

use Filament\Forms\Components\Placeholder;
use Livewire\Attributes\Reactive;
use Livewire\Component;

/**
 * MessageItem Component.
 * Represents an individual message item within a conversation.
 */
class MessageItem extends Component
{
    // Properties
    public $message;         // The current message
    #[Reactive]
    public $active;          // Indicates if the current message is active or not

    /**
     * Returns a placeholder view for the message.
     */
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.message-skeleton', $params);
    }

    /**
     * Renders the MessageItem view.
     */
    public function render()
    {
        return view('livewire.user.message-item');
    }
}
