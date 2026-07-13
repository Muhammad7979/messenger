<?php

namespace Database\Seeders;

use App\Models\ConversationMember;
use App\Models\Message;
use App\Models\MessageDelivery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MessageDeliverySeeder extends Seeder
{
    public function run(): void
    {
        $membersByConversation = ConversationMember::query()
            ->whereNull('left_at')
            ->get(['conversation_id', 'user_id'])
            ->groupBy('conversation_id');

        Message::query()
            ->orderBy('id')
            ->chunkById(200, function ($messages) use ($membersByConversation) {
                $rows = [];

                foreach ($messages as $message) {
                    $members = $membersByConversation->get($message->conversation_id, collect());

                    foreach ($members as $member) {
                        if ((int) $member->user_id === (int) $message->sender_id) {
                            continue;
                        }

                        if (! fake()->boolean(85)) {
                            continue;
                        }

                        $sentAt = Carbon::parse($message->sent_at ?? $message->created_at);

                        $rows[] = [
                            'message_id' => $message->id,
                            'user_id' => $member->user_id,
                            'delivered_at' => $sentAt->copy()->addSeconds(fake()->numberBetween(1, 120))->toDateTimeString(),
                        ];
                    }
                }

                foreach (array_chunk($rows, 500) as $chunk) {
                    MessageDelivery::query()->insert($chunk);
                }
            });
    }
}
