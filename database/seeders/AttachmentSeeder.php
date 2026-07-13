<?php

namespace Database\Seeders;

use App\Enums\MessageType;
use App\Models\Attachment;
use App\Models\Message;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    public function run(): void
    {
        Message::query()
            ->whereIn('message_type', [
                MessageType::Image,
                MessageType::Video,
                MessageType::Audio,
                MessageType::File,
                MessageType::VoiceNote,
                MessageType::Gif,
            ])
            ->orderBy('id')
            ->chunkById(200, function ($messages) {
                foreach ($messages as $message) {
                    Attachment::factory()
                        ->for($message)
                        ->forType($message->message_type)
                        ->create();
                }
            });
    }
}
