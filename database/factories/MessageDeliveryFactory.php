<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\MessageDelivery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MessageDelivery>
 */
class MessageDeliveryFactory extends Factory
{
    protected $model = MessageDelivery::class;

    public function definition(): array
    {
        return [
            'message_id' => Message::factory(),
            'user_id' => User::factory(),
            'delivered_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
