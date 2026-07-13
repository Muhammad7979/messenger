<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MessageRead>
 */
class MessageReadFactory extends Factory
{
    protected $model = MessageRead::class;

    public function definition(): array
    {
        return [
            'message_id' => Message::factory(),
            'user_id' => User::factory(),
            'read_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
