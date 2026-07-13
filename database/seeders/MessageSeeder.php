<?php

namespace Database\Seeders;

use App\Enums\MessageType;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $conversations = Conversation::query()
            ->with(['members' => fn ($q) => $q->whereNull('left_at')])
            ->get();

        $targetTotal = fake()->numberBetween(12_000, 18_000);
        $perConversation = (int) ceil($targetTotal / max($conversations->count(), 1));

        foreach ($conversations as $index => $conversation) {
            $memberIds = $conversation->members->pluck('user_id')->all();

            if ($memberIds === []) {
                continue;
            }

            $isRecent = $index % 3 === 0;
            $messageCount = $isRecent
                ? fake()->numberBetween((int) ($perConversation * 0.8), (int) ($perConversation * 1.6))
                : fake()->numberBetween((int) ($perConversation * 0.4), $perConversation);

            $start = Carbon::parse($conversation->created_at)->addHour();
            $end = $isRecent
                ? now()
                : now()->subDays(7);

            if ($end->lessThanOrEqualTo($start)) {
                $end = now();
            }

            if ($end->lessThanOrEqualTo($start)) {
                $start = $end->copy()->subDays(14);
            }

            $totalSeconds = max(1, $start->diffInSeconds($end));
            $stepSeconds = max(30, (int) ($totalSeconds / max($messageCount, 1)));
            $cursor = $start->copy();
            $lastMessageId = null;
            $messageIds = [];

            for ($i = 0; $i < $messageCount; $i++) {
                $cursor = $cursor->copy()->addSeconds($stepSeconds + fake()->numberBetween(0, 90));

                if ($cursor->greaterThan($end)) {
                    $cursor = $end->copy()->subSeconds(($messageCount - $i) * 2);
                    if ($cursor->lessThan($start)) {
                        $cursor = $start->copy()->addSeconds($i);
                    }
                }

                $type = $this->randomType();
                $senderId = fake()->randomElement($memberIds);

                $replyToId = null;
                if ($messageIds !== [] && fake()->boolean(12)) {
                    $replyToId = fake()->randomElement(array_slice($messageIds, -20));
                }

                $editedAt = null;
                if (fake()->boolean(6) && $cursor->lt(now()->subMinute())) {
                    $editedAt = $cursor->copy()->addMinutes(fake()->numberBetween(1, 40));
                    if ($editedAt->greaterThan(now())) {
                        $editedAt = now();
                    }
                }

                $message = Message::query()->create([
                    'uuid' => (string) Str::uuid(),
                    'conversation_id' => $conversation->id,
                    'sender_id' => $senderId,
                    'parent_message_id' => null,
                    'reply_to_id' => $replyToId,
                    'forwarded_from_id' => null,
                    'body' => $type === MessageType::Text || $type === MessageType::System
                        ? fake()->realTextBetween(15, 160)
                        : (fake()->boolean(25) ? fake()->sentence() : null),
                    'message_type' => $type,
                    'metadata' => null,
                    'sent_at' => $cursor,
                    'edited_at' => $editedAt,
                    'created_at' => $cursor,
                    'updated_at' => $cursor,
                ]);

                $messageIds[] = $message->id;
                $lastMessageId = $message->id;
            }

            if ($lastMessageId) {
                $conversation->update(['last_message_id' => $lastMessageId]);

                foreach ($conversation->members as $member) {
                    $readThrough = fake()->boolean(70)
                        ? $lastMessageId
                        : fake()->randomElement($messageIds);

                    $member->update([
                        'last_read_message_id' => $readThrough,
                    ]);
                }
            }
        }
    }

    private function randomType(): MessageType
    {
        $roll = fake()->numberBetween(1, 100);

        return match (true) {
            $roll <= 78 => MessageType::Text,
            $roll <= 86 => MessageType::Image,
            $roll <= 90 => MessageType::File,
            $roll <= 93 => MessageType::VoiceNote,
            $roll <= 95 => MessageType::Video,
            $roll <= 97 => MessageType::Audio,
            $roll <= 98 => MessageType::Gif,
            default => MessageType::System,
        };
    }
}
