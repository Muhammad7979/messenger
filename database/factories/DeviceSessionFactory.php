<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\DeviceSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DeviceSession>
 */
class DeviceSessionFactory extends Factory
{
    protected $model = DeviceSession::class;

    public function definition(): array
    {
        return [
            'device_id' => Device::factory(),
            'token_hash' => hash('sha256', Str::random(64)),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'last_activity_at' => fake()->dateTimeBetween('-3 days', 'now'),
            'expires_at' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'revoked_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'expires_at' => fake()->dateTimeBetween('-14 days', '-1 hour'),
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn () => [
            'revoked_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
