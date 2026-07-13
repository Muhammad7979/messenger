<?php

namespace Database\Factories;

use App\Enums\PresenceStatus;
use App\Models\Device;
use App\Models\User;
use App\Models\UserPresence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPresence>
 */
class UserPresenceFactory extends Factory
{
    protected $model = UserPresence::class;

    public function definition(): array
    {
        $status = fake()->randomElement(PresenceStatus::cases());

        return [
            'user_id' => User::factory(),
            'device_id' => Device::factory(),
            'status' => $status,
            'socket_id' => $status === PresenceStatus::Online
                ? 'sock_'.fake()->bothify('????########')
                : null,
            'last_seen' => fake()->dateTimeBetween('-2 days', 'now'),
        ];
    }

    public function online(): static
    {
        return $this->state(fn () => [
            'status' => PresenceStatus::Online,
            'socket_id' => 'sock_'.fake()->bothify('????########'),
            'last_seen' => now(),
        ]);
    }

    public function offline(): static
    {
        return $this->state(fn () => [
            'status' => PresenceStatus::Offline,
            'socket_id' => null,
        ]);
    }
}
