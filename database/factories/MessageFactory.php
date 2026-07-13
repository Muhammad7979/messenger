<?php

namespace Database\Factories;

use App\Enums\MessageType;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        $sentAt = fake()->dateTimeBetween('-3 months', 'now');

        return [
            'conversation_id' => Conversation::factory(),
            'sender_id' => User::factory(),
            'parent_message_id' => null,
            'reply_to_id' => null,
            'forwarded_from_id' => null,
            'body' => fake()->realTextBetween(20, 180),
            'message_type' => MessageType::Text,
            'metadata' => null,
            'sent_at' => $sentAt,
            'edited_at' => fake()->boolean(10) ? fake()->dateTimeBetween($sentAt, 'now') : null,
            'created_at' => $sentAt,
            'updated_at' => $sentAt,
        ];
    }

    public function text(): static
    {
        return $this->state(fn () => [
            'message_type' => MessageType::Text,
            'body' => fake()->realTextBetween(20, 200),
        ]);
    }

    public function image(): static
    {
        return $this->state(fn () => [
            'message_type' => MessageType::Image,
            'body' => fake()->optional(0.3)->sentence(),
        ]);
    }

    public function video(): static
    {
        return $this->state(fn () => [
            'message_type' => MessageType::Video,
            'body' => null,
        ]);
    }

    public function file(): static
    {
        return $this->state(fn () => [
            'message_type' => MessageType::File,
            'body' => null,
        ]);
    }

    public function voiceNote(): static
    {
        return $this->state(fn () => [
            'message_type' => MessageType::VoiceNote,
            'body' => null,
        ]);
    }

    public function system(): static
    {
        return $this->state(fn () => [
            'message_type' => MessageType::System,
            'body' => fake()->randomElement([
                'User joined the conversation',
                'User left the conversation',
                'Conversation name was updated',
            ]),
        ]);
    }
}
