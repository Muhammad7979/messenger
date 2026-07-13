<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->inRandomOrder()->limit(140)->get();

        foreach ($users as $user) {
            Device::factory()
                ->count(fake()->numberBetween(1, 3))
                ->for($user)
                ->create();
        }
    }
}
