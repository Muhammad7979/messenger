<?php

namespace Database\Factories;

use App\Enums\MemberRole;
use App\Models\Conversation;
use App\Models\ConversationMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConversationMember>
 */
class ConversationMemberFactory extends Factory
{
    protected $model = ConversationMember::class;

    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'user_id' => User::factory(),
            'role' => MemberRole::Member,
            'joined_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'left_at' => null,
            'last_read_message_id' => null,
            'is_muted' => fake()->boolean(8),
            'is_archived' => fake()->boolean(5),
        ];
    }

    public function owner(): static
    {
        return $this->state(fn () => ['role' => MemberRole::Owner]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => MemberRole::Admin]);
    }

    public function muted(): static
    {
        return $this->state(fn () => ['is_muted' => true]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['is_archived' => true]);
    }
}
