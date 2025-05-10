<?php

namespace App\Utils\Chat;

use Exception;
use Pusher\Pusher;
use Astrotomic\Twemoji\Twemoji;
use App\Models\Message;
use App\Models\Conversation;
use App\Settings\LiveChatSettings;
use Illuminate\Support\Facades\Storage;

class ChatApi
{
    public $pusher;


    /**
     * Set pusher settings
     */
    public function __construct()
    {
        $this->pusher = new Pusher(
            config('chatify.pusher.key'),
            config('chatify.pusher.secret'),
            config('chatify.pusher.app_id'),
            config('chatify.pusher.options'),
        );
    }


    /**
     * Get max file's upload size in MB.
     *
     * @return int
     */
    public function getMaxUploadSize()
    {
        return app(LiveChatSettings::class)->max_file_size * 1048576;
    }


    /**
     * This method returns the allowed image extensions
     * to attach with the message.
     *
     * @return array
     */
    public function getAllowedImages()
    {
        return explode(",", app(LiveChatSettings::class)->allowed_image_extensions);
    }


    /**
     * This method returns the allowed file extensions
     * to attach with the message.
     *
     * @return array
     */
    public function getAllowedFiles()
    {
        return explode(",", app(LiveChatSettings::class)->allowed_file_extensions);
    }


    /**
     * Returns an array contains messenger's colors
     *
     * @return array
     */
    public function getMessengerColors()
    {
        return config('chatify.colors');
    }


    /**
     * Trigger an event using Pusher
     *
     * @param string $channel
     * @param string $event
     * @param array $data
     * @return void
     */
    public function push($channel, $event, $data)
    {
        return $this->pusher->trigger($channel, $event, $data);
    }


    /**
     * Authentication for pusher
     *
     * @param User $requestUser
     * @param User $authUser
     * @param string $channelName
     * @param string $socket_id
     * @param array $data
     * @return void
     */
    public function pusherAuth($requestUser, $authUser, $channelName, $socket_id)
    {
        // Authenticated user data
        $auth_data = json_encode([
            'user_id'   => $authUser->id,
            'user_info' => [
                'uid'      => strtolower($authUser->uid),
                'username' => $authUser->username
            ]
        ]);

        // check if user authenticated
        if (auth()->check()) {

            // Check if authorized
            if ($requestUser->id == $authUser->id) {
                return $this->pusher->authorizeChannel(
                    $channelName,
                    $socket_id,
                    $auth_data
                );
            }

            // If not authorized
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // If not authenticated
        return response()->json(['message' => 'Not authenticated'], 403);
    }


    /**
     * Fetch message by id and return the message card
     * view as a response.
     *
     * @param int $id
     * @return array
     */
    public function fetchMessage($id, $index = null)
    {
        // Set empty variables
        $attachment           = null;
        $attachment_type      = null;
        $attachment_title     = null;
        $attachment_extension = null;
        $attachment_size      = null;

        // Fetch message
        $msg = Message::where('id', $id)->first();

        // Check if message does not exists
        if (!$msg) {
            return [];
        }

        // Check if message is an attachment
        if (isset($msg->attachment)) {

            // Decode attachment
            $attachment_results   = json_decode($msg->attachment);
            $attachment           = $attachment_results->new_name;
            $attachment_title     = htmlentities(trim($attachment_results->old_name), ENT_QUOTES, 'UTF-8');

            // Get file extension
            $attachment_extension = $attachment_results->extension;

            // Get file type
            if (in_array($attachment_extension, $this->getAllowedImages())) {

                // Image
                $attachment_type = 'image';
            } else {

                // File
                $attachment_type = 'file';
            }

            // Set image size
            $attachment_size      = $attachment_results->size;
        }

        // Set message
        if ($msg->content) {

            // Check if emojis enabled
            if (app(LiveChatSettings::class)->enable_emojis) {

                // Enable emojis
                $message = Twemoji::text($msg->content)->toHtml();
            } else {

                // Not enabled
                $message = clean($msg->content);
            }
        } else {

            // No message
            $message = null;
        }

        // Return message
        return [
            'index'           => $index,
            'id'              => $msg->id,
            'from_id'         => $msg->sender_id,
            'to_id'           => $msg->receiver_id,
            'message'         => $message,
            'is_negotiable_conversation' => $msg->is_negotiable_conversation,
            'attachment'      => [$attachment, $attachment_title, $attachment_type, $attachment_extension, $attachment_size],
            'time'            => format_date($msg->created_at),
            'fullTime'        => $msg->created_at,
            'viewType'        => ($msg->sender_id == auth()->id()) ? 'sender' : 'default',
            'seen'            => $msg->seen,
        ];
    }


    /**
     * Return a message card with the given data.
     *
     * @param array $data
     * @param string $viewType
     * @return string
     */
    public function messageCard($data, $viewType = null)
    {
        if (!$data) {
            return '';
        }
        $data['viewType'] = ($viewType) ? $viewType : $data['viewType'];
        return view('Chatify::layouts.messageCard', $data)->render();
    }


    /**
     * Default fetch messages query between a Sender and Receiver.
     *
     * @param int $user_id
     * @return Message|\Illuminate\Database\Eloquent\Builder
     */
    public function fetchMessagesQuery($conversation_id)
    {
        $userId = auth()->id();
        return Message::where('conversation_id', $conversation_id)
            ->whereHas('conversation', function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    $q->where('buyer_id', $userId)
                        ->orWhere('seller_id', $userId);
                });
            });
    }


    /**
     * create a new message to database
     *
     * @param array $data
     * @return void
     */
    public function newMessage($data)
    {
        $message             = new Message();
        $message->id    = $data['id'];
        $message->sender_id    = $data['sender_id'];
        $message->receiver_id      = $data['receiver_id'];
        $message->conversation_id      = $data['conversation_id'];
        $message->content       = clean(strip_tags($data['content']));
        $message->attachment = $data['attachment'];
        $message->is_negotiable_conversation = $data['is_negotiable_conversation'];
        if(isset($data['created_at'])){
            $message->created_at =$data['created_at'];
        }
        $message->save();
    }

    /**
     * Make messages in a specific conversation as seen.
     *
     * @param int $conversation_id
     * @return bool
     */
    public function makeSeen($conversation_id)
    {
        // Fetch the conversation
        $conversation = Conversation::find($conversation_id);
        if (!$conversation) {
            return false; // Conversation not found
        }

        // Determine whether the authenticated user is the buyer or the seller
        $userId = auth()->id();
        $isBuyer = $conversation->buyer_id == $userId;

        // Update messages as seen
        Message::where('conversation_id', $conversation_id)
            ->where('sender_id', $isBuyer ? $conversation->seller_id : $conversation->buyer_id)
            ->where('receiver_id', $userId)
            ->where('seen', false)
            ->update(['seen' => true]);

        // Success
        return true;
    }


    /**
     * Get last message for a specific user
     *
     * @param int $user_id
     * @return Message|Collection|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getLastMessageQuery($conversationId)
    {
        return Message::where('conversation_id', $conversationId)
            ->latest('updated_at')
            ->first();
    }



    /**
     * Count Unseen messages
     *
     * @param int $user_id
     * @return Collection
     */
    public function countUnseenMessages($conversation_id)
    {

        // Fetch the conversation
        $conversation = Conversation::find($conversation_id);
        if (!$conversation) {
            return false; // Conversation not found
        }

        // Determine whether the authenticated user is the buyer or the seller
        $userId = auth()->id();
        $isBuyer = $conversation->buyer_id == $userId;

        // Update messages as seen
        return Message::where('conversation_id', $conversation_id)
            ->where('sender_id', $isBuyer ? $conversation->seller_id : $conversation->buyer_id)
            ->where('receiver_id', $userId)
            ->where('seen', false)
            ->count();
    }



    /**
     * Get user list's item data [Contact Itme]
     * (e.g. User data, Last message, Unseen Counter...)
     *
     * @param int $messenger_id
     * @param Collection $user
     * @return string
     */
    public function getContactItem($contact, $conversationId)
    {
        // get last message
        $lastMessage   = $this->getLastMessageQuery($conversationId);

        // Get Unseen messages counter
        $unseenCounter = $this->countUnseenMessages($conversationId);

        return view('Chatify::layouts.listItem', [
            'get'           => 'users',
            'contact'          => $contact,
            'lastMessage'   => $lastMessage,
            'unseenCounter' => $unseenCounter,
        ])->render();
    }


    /**
     * Get user with avatar (formatted).
     *
     * @param Collection $user
     * @return Collection
     */
    public function getUserWithAvatar($user)
    {
        $user->avatar_src = $user->avatar;
        return $user;
    }

    /**
     * Get shared photos of the conversation
     *
     * @param int $user_id
     * @return array
     */
    public function getSharedPhotos($user_id)
    {
        $images = array(); // Default
        // Get messages
        $msgs = $this->fetchMessagesQuery($user_id)->orderBy('created_at', 'DESC');
        if ($msgs->count() > 0) {
            foreach ($msgs->get() as $msg) {
                // If message has attachment
                if ($msg->attachment) {
                    $attachment = json_decode($msg->attachment);
                    // determine the type of the attachment
                    in_array(pathinfo($attachment->new_name, PATHINFO_EXTENSION), $this->getAllowedImages())
                        ? array_push($images, $attachment->new_name) : '';
                }
            }
        }
        return $images;
    }


    /**
     * Delete Conversation
     *
     * @param int $conversation_id
     * @return boolean
     */
    public function deleteConversation($conversation_id)
    {
        try {
            // Fetch the conversation
            $conversation = Conversation::with('messages')->find($conversation_id);

            // Check if the conversation exists
            if (!$conversation) {
                return false; // Conversation not found
            }

            // Mark as deleted by the buyer or seller
            if (auth()->id() === $conversation->buyer_id) {
                $conversation->deleted_by_buyer_at = now();
            } elseif (auth()->id() === $conversation->seller_id) {
                $conversation->deleted_by_seller_at = now();
            } else {
                return false; // User not part of the conversation
            }
            $conversation->save();

            // Check if both buyer and seller have deleted the conversation
            if ($conversation->deleted_by_buyer_at && $conversation->deleted_by_seller_at) {
                // Delete all messages and attachments
                foreach ($conversation->messages as $msg) {
                    // Delete file attached if exist
                    if (isset($msg->attachment)) {
                        $path = config('chatify.attachments.folder') . '/' . json_decode($msg->attachment)->new_name;
                        if (Storage::exists($path)) {
                            Storage::delete($path);
                        }
                    }

                    // Delete message from database
                    $msg->delete();
                }

                // Finally, delete the conversation itself
                $conversation->delete();
            }

            // Success
            return true;
        } catch (Exception $e) {
            // Error
            return false;
        }
    }


    /**
     * Delete message by ID
     *
     * @param int $id
     * @return boolean
     */
    public function deleteMessage($id)
    {
        try {

            // Get message
            $message = Message::where('sender_id', auth()->id())->where('id', $id)->firstOrFail();

            // Check if message has attachment
            if (isset($message->attachment)) {

                // Get file path
                $path = config('chatify.attachments.folder') . '/' . json_decode($message->attachment)->new_name;

                // Delete file attached if exist
                if (self::storage()->exists($path)) {
                    self::storage()->delete($path);
                }
            }

            // delete from database
            $message->delete();

            // Success
            return true;
        } catch (Exception $e) {

            // Not found
            return false;
        }
    }


    /**
     * Return a storage instance with disk name specified in the config.
     *
     */
    public function storage()
    {
        return Storage::disk(config('chatify.storage_disk_name'));
    }


    /**
     * Get user avatar url.
     *
     * @param string $user_avatar_name
     * @return string
     */
    public function getUserAvatarUrl($user_avatar_name)
    {
        return self::storage()->url(config('chatify.user_avatar.folder') . '/' . $user_avatar_name);
    }


    /**
     * Get attachment's url.
     *
     * @param string $attachment_name
     * @return string
     */
    public function getAttachmentUrl($attachment_name)
    {
        return self::storage()->url(config('chatify.attachments.folder') . '/' . $attachment_name);
    }
}
