<?php

namespace Database\Factories;

use App\Enums\DevicePlatform;
use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        $platform = fake()->randomElement(DevicePlatform::cases());

        $names = match ($platform) {
            DevicePlatform::Android => ['Pixel 8', 'Galaxy S24', 'OnePlus 12'],
            DevicePlatform::Ios => ['iPhone 15', 'iPhone 16 Pro', 'iPad Pro'],
            DevicePlatform::Web => ['Chrome on Windows', 'Firefox on macOS', 'Safari on macOS'],
            DevicePlatform::Windows => ['Desktop PC', 'Surface Laptop'],
            DevicePlatform::Macos => ['MacBook Pro', 'MacBook Air'],
            DevicePlatform::Linux => ['Ubuntu Desktop', 'Fedora Workstation'],
        };

        return [
            'user_id' => User::factory(),
            'platform' => $platform,
            'device_name' => fake()->randomElement($names),
            'device_model' => fake()->optional(0.7)->bothify('Model-##??'),
            'os_version' => fake()->optional(0.8)->numerify('#.#.#'),
            'app_version' => fake()->numerify('#.#.#'),
            'push_token' => $platform->isMobile() ? hash('sha256', fake()->uuid()) : null,
            'ip_address' => fake()->ipv4(),
            'last_login_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'last_seen_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'is_active' => fake()->boolean(90),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['is_active' => true]);
    }

    public function web(): static
    {
        return $this->state(fn () => [
            'platform' => DevicePlatform::Web,
            'device_name' => 'Chrome on Windows',
            'push_token' => null,
        ]);
    }
}
