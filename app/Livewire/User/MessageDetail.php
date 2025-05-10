<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Url;
use phpDocumentor\Reflection\Types\This;

/**
 * MessageDetail Component.
 * Represents the detailed view of a user's conversation and messages.
 */
class MessageDetail extends Component
{
    // Properties
    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';
    #[Reactive]
    public $conversation_id;
    public $messages = [];
    public $conversation;
    public $receiverId;
    public $newMessage = '';
    public $isOffer = 0;
    public $lastOfferMessage;
    public $newOffer;
    public $isAcceptOffer = 0;

    /**
     * Component's mount lifecycle hook.
     * Loads the initial conversation based on the provided ID.
     */
    public function mount($conversation_id)
    {
        $this->conversation = Conversation::find($conversation_id);
        if ($this->conversation) {
            $this->receiverId = (Auth::id() == $this->conversation->buyer_id) ? $this->conversation->seller_id : $this->conversation->buyer_id;
        }

        $this->loadConversation($conversation_id);
    }

    /**
     * Appends a new line to the current message.
     */
    public function addLine($currentMessage)
    {
        $this->newMessage = $currentMessage . "\n";
    }

    /**
     * Loads the conversation details and fetches messages.
     */
    #[On('message-deleted')]
    #[On('message-opened')]
    public function loadConversation()
    {
        $this->fetchMessages();
    }

    /**
     * Fetches the messages for the current conversation.
     */
    private function fetchMessages()
    {
        $this->conversation = Conversation::find($this->conversation_id);
        $this->messages = Message::where('conversation_id', $this->conversation_id)
            ->orderBy('created_at', 'asc')
            ->get();

        $this->lastOfferMessage = Message::where('conversation_id', $this->conversation_id)
            ->where('is_negotiable_conversation', true)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function customMessage($messages, $isOffer = 0, $isAcceptOffer = 0)
    {
        $this->newMessage = ($isOffer && !$isAcceptOffer && !$messages == 'Give me a better offer') ? 'Negotiation value of ' . config('app.currency_symbol') . $messages : $messages;
        $this->isOffer = $isOffer;
        $this->isAcceptOffer = $isAcceptOffer;

        if ($this->isOffer) {
            $this->sendMessage();
        }
    }

    public function newOfferSend()
    {
        $this->customMessage($this->newOffer, 1);
    }

    /**
     * Returns a placeholder view for the messages.
     */
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.message-skeleton', $params);
    }

    /**
     * Sends a new message in the current conversation.
     */
    public function sendMessage()
    {
        $conversation = Conversation::find($this->conversation_id);
        if (!$conversation) {
            // Handle error: Conversation not found.
            return;
        }
        $receiver_id = (Auth::id() == $conversation->buyer_id) ? $conversation->seller_id : $conversation->buyer_id;
        
        if (trim($this->newMessage)) {
            Message::create([
                'conversation_id' => $this->conversation_id,
                'sender_id' => Auth::id(),
                'receiver_id' => $receiver_id,
                'content' => $this->newMessage,
                'is_negotiable_conversation' => $this->isOffer,
                'is_accept_offer' => $this->isAcceptOffer
            ]);
        }

        // $this->notifyReceiver($receiver_id);
        $this->newMessage = '';
        $this->fetchMessages();
    }


    /**
     * Renders the MessageDetail view.
     */
    public function render()
    {
        return view('livewire.user.message-detail');
    }
}
