<?php
namespace App\Filament\App\Pages;

use App\Models\Conversation;
use App\Models\Message;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Filament\Pages\Page;

/**
 * MyMessages Component.
 * Represents the user's message interface for viewing and managing conversations.
 */
class MyMessages extends Page implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';

    public $messages = [];
    public $activeMessageId = null;
    #[Url]
    public $conversation_id;
    public $isMobile;

    protected static string $view = 'filament.app.pages.my-messages';

    public static function getNavigationGroup(): ?string
    {
        return __('messages.t_insights_navigation');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_my_messages');
    }
    /**
     * Component's mount lifecycle hook.
     * Checks user's access to a conversation and fetches messages.
     */
    public function mount()
    {
        $this->checkAccessToConversation();
        $this->fetchMessages();
    }

    /**
     * Checks if the user has access to the specified conversation.
     */
    private function checkAccessToConversation()
    {
        if (!$this->conversation_id) {
            return;
        }

        $conversation = Conversation::find($this->conversation_id);
        if (!$conversation) {
            Notification::make()
                ->title(__('messages.t_conversation_doesnt_exist'))
                ->danger()
                ->send();
            return redirect(route('my-messages'));
        }

        $userId = Auth::id();
        if ($userId != $conversation->buyer_id && $userId != $conversation->seller_id) {
            Notification::make()
                ->title(__('messages.t_no_permission_for_messages'))
                ->danger()
                ->send();
            return redirect(route('home'));
        }
    }

    /**
     * Fetches the messages for the authenticated user.
     */
    public function fetchMessages() {
        $userId = Auth::id();

        //Fetch conversations for the authenticated user where they haven't deleted it.
        $conversations = Conversation::with(['messages' => function($query) {
            $query->latest('updated_at')->take(1);
        }])
        ->where(function ($query) use ($userId) {
            $query->where('buyer_id', $userId)
                ->whereNull('deleted_by_buyer_at'); // Ensure the conversation is not deleted by the buyer.
        })
        ->orWhere(function ($query) use ($userId) {
            $query->where('seller_id', $userId)
                ->whereNull('deleted_by_seller_at'); // Ensure the conversation is not deleted by the seller.
        })
        ->orderByDesc(
            Message::select('updated_at')
                ->whereColumn('conversations.id', 'messages.conversation_id')
                ->orderBy('updated_at', 'desc')
                ->limit(1)
        )
        ->get();


        $latestMessages = collect();

        foreach ($conversations as $conversation) {
            $latestMessage = $conversation->messages()->latest('updated_at')->first();
            if ($latestMessage) {
                $latestMessages->push($latestMessage);
            }
        }

        $this->messages = $latestMessages;
        $this->conversation_id = $this->conversation_id ?? $latestMessages->first()?->conversation_id;
       // Check if conversation_id is defined
        if ($this->conversation_id) {
            // Retrieve the latest message ID for the specific conversation
            $latestMessageForConversation = Message::where('conversation_id', $this->conversation_id)
                                                ->latest('updated_at')
                                                ->first();
            $this->activeMessageId = $latestMessageForConversation?->id ?? null;
        } else {
            // Default to the first message ID from the latestMessages collection
            $this->activeMessageId = $latestMessages->first()?->id;
        }
    }

     /**
     * Opens a specific message in the interface.
     *
     * @param array $message The message details.
     * @param bool $isMobile Whether the device is mobile or not.
     */
    public function openMessage($message, $isMobile) {
        $this->isMobile = $isMobile;
        $this->activeMessageId = $message['id'];
        $this->conversation_id = $message['conversation_id'];
        $this->referrer = '/my-messages';
        $this->dispatch('message-opened');
    }

    /**
     * Deletes a specific conversation.
     *
     * @param Conversation $conversation The conversation to delete.
     */
    public function deleteConversation(Conversation $conversation)
    {
        if (Auth::id() === $conversation->buyer_id) {
            $conversation->deleted_by_buyer_at = now();
            $conversation->save();
        } elseif (Auth::id() === $conversation->seller_id) {
            $conversation->deleted_by_seller_at = now();
            $conversation->save();
        }
        $this->conversation_id = null;
        $this->fetchMessages();
        $this->dispatch('message-opened');
    }

    /**
     * Defines the delete action for a conversation.
     *
     * @return Action
     */
    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label(__('messages.t_delete'))
            ->modalHeading(__('messages.t_delete'))
            ->requiresConfirmation()
            ->icon('recycle-bin-2')
            ->iconButton()
            ->size(ActionSize::Large)
            ->action(function (array $arguments) {
                $conversation = Conversation::find($this->conversation_id);
                $this->deleteConversation($conversation);
            });
    }
}
