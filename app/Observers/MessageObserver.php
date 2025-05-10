<?php

namespace App\Observers;

use App\Models\AutoResponse;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\User\MessageSentNotification;
use App\Settings\LiveChatSettings;
use App\Utils\Chat\ChatApi;
use Illuminate\Support\Carbon;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message)
    {
        $existingMessagesCount = Message::where('conversation_id', $message->conversation_id)->count() - 1; // Subtract 1 to exclude the current message

        $conversation = Conversation::find($message->conversation_id);
        $buyerName = $conversation->buyer->name;
        $productName = $conversation->ad->title;
        // Trigger notification to the receiver
        $receiver = $message->receiver;
        $this->sendAutoMessage($message);
        try {
            $receiver->notify(new MessageSentNotification($message, $existingMessagesCount, $buyerName, $productName));
        } catch (\Exception $e) {
            \Log::error('Failed to send notification', [
                'error' => $e->getMessage(),
            ]);
            // Optionally, notify admin or take fallback action
        }

    }


    public function sendAutoMessage($message)
    {
        // Retrieve the conversation of the message
        $conversation = Conversation::find($message->conversation_id);

        if (!$conversation) {
            return; // If conversation does not exist, exit
        }

        // Check if this is the first message in the conversation
        $isFirstMessage = Message::where('conversation_id', $message->conversation_id)->count() === 1;
        if ($isFirstMessage && getSubscriptionSetting('status') && getUserSubscriptionPlan($conversation->seller_id)?->automated_messages) {
            // Auto-response setup: fetch or define the message
            $autoResponseText = AutoResponse::where('user_id', $message->receiver_id)->value('message');
            if ($autoResponseText) {
                if (app('filament')->hasPlugin('live-chat') && app(LiveChatSettings::class)->enable_livechat) {
                    $this->createLiveAutoMessage($message,$autoResponseText);
                }else{
                    // Send the auto-response message
                    Message::create([
                        'sender_id' => $message->receiver_id, // Auto-response from the receiver
                        'receiver_id' => $message->sender_id,   // Sent to the original message sender
                        'content' => $autoResponseText ?: 'Thank you for reaching out!',
                        'is_read' => false,
                        'conversation_id' => $message->conversation_id,
                        'created_at' => Carbon::now()->addSeconds(3),
                    ]);
                }
            }
        }
    }

    /**
     *
     * Send live message using chatify if live chat enabled
     * @param mixed $message
     * @param mixed $autoResponseText
     *
     */
    public function createLiveAutoMessage($message,$autoResponseText){
        $conversation = Conversation::find($message->conversation_id);
        if (!$conversation) {
            return response()->json([
                'status' => '404',
                'error' => ['status' => 1, 'message' => 'Conversation not found.']
            ]);
        }

        // Determine the receiver ID based on the conversation
        $receiverId = $conversation->buyer_id == auth()->id() ? $conversation->seller_id : $conversation->buyer_id;

        // Generate message id
        $message_id = mt_rand(9, 999999999) + time();
        $chat = new ChatApi();
        // Save message
        $chat->newMessage([
            'id' => $message_id,
            'conversation_id' => $conversation->id,
            'sender_id' => $message->receiver_id, // Auto-response from the receiver
            'receiver_id' => $message->sender_id,   // Sent to the original message sender
            'attachment' =>null,
            'content' => $autoResponseText ?: 'Thank you for reaching out!',
            'is_negotiable_conversation' => 0,
            'created_at' => Carbon::now()->addSeconds(3),

        ]);

        // fetch message to send it with the response
        $messageData = $chat->fetchMessage($message_id);

        // Send to user using pusher
        $chat->push("private-chat.".$receiverId, 'messaging', [
            'from_id' => $conversation->id,
            'to_id' => $receiverId,
            'message' => $chat->messageCard($messageData, 'default'),
            'is_negotiable_conversation' => 0
        ]);
    }
}
