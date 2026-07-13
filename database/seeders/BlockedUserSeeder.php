<?php

namespace Database\Seeders;

use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlockedUserSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::query()->pluck('id')->all();
        $pairs = [];

        for ($i = 0; $i < 40; $i++) {
            $blocker = fake()->randomElement($userIds);
            $blocked = fake()->randomElement($userIds);

            if ($blocker === $blocked) {
                continue;
            }

            $key = $blocker.':'.$blocked;
            if (isset($pairs[$key])) {
                continue;
            }

            $pairs[$key] = true;

            BlockedUser::query()->firstOrCreate([
                'blocker_id' => $blocker,
                'blocked_id' => $blocked,
            ]);
        }
    }
}
