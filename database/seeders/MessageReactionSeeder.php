<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\MessageReaction;
use Illuminate\Database\Seeder;

class MessageReactionSeeder extends Seeder
{
    public function run(): void
    {
        $emojis = ['👍', '❤️', '😂', '😮', '😢', '🔥', '👏', '🎉'];

        Message::query()
            ->inRandomOrder()
            ->limit(2500)
            ->with('conversation.members')
            ->get()
            ->each(function (Message $message) use ($emojis) {
                $members = $message->conversation?->members ?? collect();

                if ($members->isEmpty()) {
                    return;
                }

                $reactors = $members->random(min($members->count(), fake()->numberBetween(1, 3)));

                foreach (collect($reactors) as $member) {
                    MessageReaction::query()->firstOrCreate(
                        [
                            'message_id' => $message->id,
                            'user_id' => $member->user_id,
                            'emoji' => fake()->randomElement($emojis),
                        ],
                        [
                            'created_at' => fake()->dateTimeBetween($message->sent_at ?? '-1 month', 'now'),
                        ]
                    );
                }
            });
    }
}
