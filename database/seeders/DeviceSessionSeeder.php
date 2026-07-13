<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\DeviceSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeviceSessionSeeder extends Seeder
{
    public function run(): void
    {
        $devices = Device::query()
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(120)
            ->get();

        foreach ($devices as $device) {
            DeviceSession::factory()
                ->for($device)
                ->create([
                    'token_hash' => hash('sha256', Str::random(64)),
                    'ip_address' => $device->ip_address ?? fake()->ipv4(),
                ]);
        }
    }
}
