<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\MessageDelivery;
use App\Models\MessageRead;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MessageReadSeeder extends Seeder
{
    public function run(): void
    {
        Message::query()
            ->orderBy('id')
            ->chunkById(150, function ($messages) {
                $messageIds = $messages->pluck('id')->all();

                $deliveries = MessageDelivery::query()
                    ->whereIn('message_id', $messageIds)
                    ->get()
                    ->groupBy('message_id');

                $rows = [];

                foreach ($messages as $message) {
                    foreach ($deliveries->get($message->id, collect()) as $delivery) {
                        if (! fake()->boolean(70)) {
                            continue;
                        }

                        $deliveredAt = Carbon::parse($delivery->delivered_at);

                        $rows[] = [
                            'message_id' => $delivery->message_id,
                            'user_id' => $delivery->user_id,
                            'read_at' => $deliveredAt->copy()->addSeconds(fake()->numberBetween(1, 600)),
                        ];
                    }
                }

                foreach (array_chunk($rows, 500) as $chunk) {
                    MessageRead::query()->insert($chunk);
                }
            });
    }
}
