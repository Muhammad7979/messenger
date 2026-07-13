<?php

namespace Database\Seeders;

use App\Models\ConversationMember;
use App\Models\Device;
use App\Models\TypingStatus;
use Illuminate\Database\Seeder;

class TypingStatusSeeder extends Seeder
{
    public function run(): void
    {
        $memberships = ConversationMember::query()
            ->whereNull('left_at')
            ->inRandomOrder()
            ->limit(15)
            ->get();

        foreach ($memberships as $membership) {
            $device = Device::query()
                ->where('user_id', $membership->user_id)
                ->where('is_active', true)
                ->inRandomOrder()
                ->first();

            TypingStatus::query()->updateOrCreate(
                [
                    'conversation_id' => $membership->conversation_id,
                    'user_id' => $membership->user_id,
                ],
                [
                    'device_id' => $device?->id,
                    'started_at' => now(),
                    'expires_at' => now()->addSeconds(5),
                ]
            );
        }
    }
}
