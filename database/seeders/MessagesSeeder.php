<?php
namespace Database\Seeders;

use App\Models\Ad;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected $sampleMessages = [
        "Is this item still available?",
        "Can the price be negotiated?",
        "When can I come to see the item?",
        "Can you provide more details about the condition?",
        "Is it possible for delivery?",
        "How old is this item?"
    ];

    public function run(): void
    {
        $ads = Ad::all();

        foreach ($ads as $ad) {
            $potentialInterestedUsers = User::whereNotIn('id', [$ad->user_id])
                                            ->inRandomOrder()
                                            ->take(rand(1, 3))
                                            ->get();

            foreach ($potentialInterestedUsers as $interestedUser) {
                if ($interestedUser->id === $ad->user_id) {
                    // Skip this iteration if the interested user is the ad owner
                    continue;
                }
                // First, we seed a conversation.
                $conversation = Conversation::firstOrCreate([
                    'ad_id' => $ad->id,
                    'buyer_id' => $interestedUser->id,
                    'seller_id' => $ad->user_id
                ], [
                    'last_updated' => now()
                ]);

                $messageContent = $this->sampleMessages[array_rand($this->sampleMessages)];

                // Next, we associate the message with the conversation.
                $conversation->messages()->create([
                    'content' => $messageContent,
                    'sender_id' => $interestedUser->id,
                    'receiver_id' => $ad->user_id,
                    'is_read' => rand(0, 1)  // Randomly set some messages as read
                ]);
            }
        }
    }
}
