<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DeviceSeeder::class,
            UserPresenceSeeder::class,
            ConversationSeeder::class,
            ConversationMemberSeeder::class,
            MessageSeeder::class,
            AttachmentSeeder::class,
            MessageDeliverySeeder::class,
            MessageReadSeeder::class,
            MessageReactionSeeder::class,
            PinnedMessageSeeder::class,
            BlockedUserSeeder::class,
            TypingStatusSeeder::class,
            DeviceSessionSeeder::class,
        ]);
    }
}
