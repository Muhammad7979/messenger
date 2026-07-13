<?php

namespace Database\Factories;

use App\Enums\AttachmentStorage;
use App\Enums\MessageType;
use App\Models\Attachment;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        $type = fake()->randomElement([
            MessageType::Image,
            MessageType::Video,
            MessageType::Audio,
            MessageType::File,
            MessageType::VoiceNote,
            MessageType::Gif,
        ]);

        return array_merge([
            'message_id' => Message::factory(),
            'storage' => AttachmentStorage::Local,
            'checksum' => hash('sha256', Str::random(40)),
        ], $this->attributesForType($type));
    }

    public function forType(MessageType $type): static
    {
        return $this->state(fn () => $this->attributesForType($type));
    }

    /**
     * @return array<string, mixed>
     */
    private function attributesForType(MessageType $type): array
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $hash = Str::random(16);

        return match ($type) {
            MessageType::Image, MessageType::Gif => [
                'path' => "uploads/messages/{$year}/{$month}/{$hash}.jpg",
                'original_name' => fake()->word().'.jpg',
                'mime_type' => $type === MessageType::Gif ? 'image/gif' : 'image/jpeg',
                'size' => fake()->numberBetween(50_000, 2_500_000),
                'width' => fake()->numberBetween(640, 1920),
                'height' => fake()->numberBetween(480, 1080),
                'duration' => null,
            ],
            MessageType::Video => [
                'path' => "uploads/messages/{$year}/{$month}/{$hash}.mp4",
                'original_name' => fake()->word().'.mp4',
                'mime_type' => 'video/mp4',
                'size' => fake()->numberBetween(1_000_000, 40_000_000),
                'width' => fake()->randomElement([1280, 1920]),
                'height' => fake()->randomElement([720, 1080]),
                'duration' => fake()->numberBetween(5, 300),
            ],
            MessageType::Audio, MessageType::VoiceNote => [
                'path' => "uploads/messages/{$year}/{$month}/{$hash}.m4a",
                'original_name' => ($type === MessageType::VoiceNote ? 'voice-note' : fake()->word()).'.m4a',
                'mime_type' => 'audio/mp4',
                'size' => fake()->numberBetween(20_000, 2_000_000),
                'width' => null,
                'height' => null,
                'duration' => fake()->numberBetween(2, 180),
            ],
            default => [
                'path' => "uploads/messages/{$year}/{$month}/{$hash}.pdf",
                'original_name' => fake()->words(2, true).'.pdf',
                'mime_type' => 'application/pdf',
                'size' => fake()->numberBetween(10_000, 5_000_000),
                'width' => null,
                'height' => null,
                'duration' => null,
            ],
        };
    }
}
