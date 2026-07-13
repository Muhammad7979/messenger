<?php

namespace Database\Seeders;

use App\Enums\ConversationType;
use App\Enums\MemberRole;
use App\Models\Conversation;
use App\Models\ConversationMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ConversationMemberSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::query()->pluck('id')->all();
        $conversations = Conversation::query()->get();

        foreach ($conversations as $conversation) {
            if ($conversation->type === ConversationType::Private) {
                $this->seedPrivateMembers($conversation, $userIds);
            } else {
                $this->seedGroupMembers($conversation, $userIds);
            }
        }
    }

    /**
     * @param  list<int>  $userIds
     */
    private function seedPrivateMembers(Conversation $conversation, array $userIds): void
    {
        $creatorId = (int) $conversation->created_by;
        $otherId = (int) collect($userIds)
            ->reject(fn ($id) => (int) $id === $creatorId)
            ->random();

        $joinedAt = Carbon::parse($conversation->created_at);

        foreach ([$creatorId, $otherId] as $userId) {
            ConversationMember::query()->create([
                'conversation_id' => $conversation->id,
                'user_id' => $userId,
                'role' => MemberRole::Member,
                'joined_at' => $joinedAt,
                'is_muted' => fake()->boolean(5),
                'is_archived' => fake()->boolean(4),
                'created_at' => $joinedAt,
                'updated_at' => $joinedAt,
            ]);
        }
    }

    /**
     * @param  list<int>  $userIds
     */
    private function seedGroupMembers(Conversation $conversation, array $userIds): void
    {
        $size = fake()->numberBetween(3, 25);
        $members = collect($userIds)->shuffle()->take($size)->values();

        if (! $members->contains($conversation->created_by)) {
            $members[0] = $conversation->created_by;
            $members = $members->unique()->values();
        }

        $joinedAt = Carbon::parse($conversation->created_at);
        $adminCount = min(2, max(0, $members->count() - 1));

        foreach ($members as $index => $userId) {
            $role = match (true) {
                (int) $userId === (int) $conversation->created_by => MemberRole::Owner,
                $index <= $adminCount && (int) $userId !== (int) $conversation->created_by => MemberRole::Admin,
                fake()->boolean(8) => MemberRole::Moderator,
                default => MemberRole::Member,
            };

            // Ensure only one owner (creator)
            if ($role === MemberRole::Owner && (int) $userId !== (int) $conversation->created_by) {
                $role = MemberRole::Member;
            }

            ConversationMember::query()->create([
                'conversation_id' => $conversation->id,
                'user_id' => $userId,
                'role' => $role,
                'joined_at' => $joinedAt->copy()->addMinutes($index),
                'is_muted' => fake()->boolean(10),
                'is_archived' => fake()->boolean(6),
                'created_at' => $joinedAt,
                'updated_at' => $joinedAt,
            ]);
        }
    }
}
