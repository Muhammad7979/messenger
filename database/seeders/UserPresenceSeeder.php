<?php

namespace Database\Seeders;

use App\Enums\PresenceStatus;
use App\Models\Device;
use App\Models\UserPresence;
use Illuminate\Database\Seeder;

class UserPresenceSeeder extends Seeder
{
    public function run(): void
    {
        $devicesByUser = Device::query()
            ->where('is_active', true)
            ->get()
            ->groupBy('user_id');

        foreach ($devicesByUser as $userId => $devices) {
            $device = $devices->random();
            $status = fake()->randomElement([
                PresenceStatus::Online,
                PresenceStatus::Offline,
                PresenceStatus::Away,
                PresenceStatus::Busy,
            ]);

            UserPresence::query()->updateOrCreate(
                ['user_id' => $userId],
                [
                    'device_id' => $device->id,
                    'status' => $status,
                    'socket_id' => $status === PresenceStatus::Online
                        ? 'sock_'.fake()->bothify('????########')
                        : null,
                    'last_seen' => fake()->dateTimeBetween('-2 days', 'now'),
                ]
            );
        }
    }
}
