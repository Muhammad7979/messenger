<?php

namespace Database\Seeders;

use App\Enums\ConversationType;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::query()->pluck('id');

        for ($i = 0; $i < 100; $i++) {
            Conversation::factory()
                ->private()
                ->create([
                    'created_by' => $userIds->random(),
                    'created_at' => fake()->dateTimeBetween('-8 months', '-1 week'),
                ]);
        }

        for ($i = 0; $i < 40; $i++) {
            Conversation::factory()
                ->group()
                ->create([
                    'type' => ConversationType::Group,
                    'created_by' => $userIds->random(),
                    'created_at' => fake()->dateTimeBetween('-8 months', '-1 day'),
                ]);
        }
    }
}
