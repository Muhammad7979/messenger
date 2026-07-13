<?php

namespace Database\Seeders;

use App\Enums\ConversationType;
use App\Enums\MemberRole;
use App\Models\Conversation;
use App\Models\MessagePin;
use Illuminate\Database\Seeder;

class PinnedMessageSeeder extends Seeder
{
    public function run(): void
    {
        $groups = Conversation::query()
            ->where('type', ConversationType::Group)
            ->with(['members', 'messages' => fn ($q) => $q->latest('id')->limit(30)])
            ->get();

        foreach ($groups as $conversation) {
            if ($conversation->messages->isEmpty()) {
                continue;
            }

            $pinCount = fake()->numberBetween(0, 3);

            if ($pinCount === 0 || $conversation->messages->isEmpty()) {
                continue;
            }

            $candidates = $conversation->messages->random(min($pinCount, $conversation->messages->count()));

            $pinners = $conversation->members
                ->filter(fn ($m) => in_array($m->role, [MemberRole::Owner, MemberRole::Admin, MemberRole::Moderator], true))
                ->values();

            if ($pinners->isEmpty()) {
                $pinners = $conversation->members;
            }

            foreach (collect($candidates) as $message) {
                MessagePin::query()->firstOrCreate(
                    [
                        'conversation_id' => $conversation->id,
                        'message_id' => $message->id,
                    ],
                    [
                        'pinned_by' => $pinners->random()->user_id,
                        'pinned_at' => fake()->dateTimeBetween($message->sent_at ?? '-1 month', 'now'),
                    ]
                );
            }
        }
    }
}
