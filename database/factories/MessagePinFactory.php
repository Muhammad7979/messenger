<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessagePin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MessagePin>
 */
class MessagePinFactory extends Factory
{
    protected $model = MessagePin::class;

    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'message_id' => Message::factory(),
            'pinned_by' => User::factory(),
            'pinned_at' => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
