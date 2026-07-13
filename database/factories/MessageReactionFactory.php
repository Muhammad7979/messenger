<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MessageReaction>
 */
class MessageReactionFactory extends Factory
{
    protected $model = MessageReaction::class;

    public function definition(): array
    {
        return [
            'message_id' => Message::factory(),
            'user_id' => User::factory(),
            'emoji' => fake()->randomElement(['👍', '❤️', '😂', '😮', '😢', '🔥', '👏', '🎉']),
            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
