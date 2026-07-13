<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Device;
use App\Models\TypingStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TypingStatus>
 */
class TypingStatusFactory extends Factory
{
    protected $model = TypingStatus::class;

    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-1 minute', 'now');

        return [
            'conversation_id' => Conversation::factory(),
            'user_id' => User::factory(),
            'device_id' => Device::factory(),
            'started_at' => $startedAt,
            'expires_at' => (clone $startedAt)->modify('+5 seconds'),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'started_at' => now(),
            'expires_at' => now()->addSeconds(5),
        ]);
    }
}
