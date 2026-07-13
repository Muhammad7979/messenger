<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->foreignId('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();

            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('parent_message_id')
                ->nullable()
                ->constrained('messages')
                ->nullOnDelete();

            $table->foreignId('reply_to_id')
                ->nullable()
                ->constrained('messages')
                ->nullOnDelete();

            $table->foreignId('forwarded_from_id')
                ->nullable()
                ->constrained('messages')
                ->nullOnDelete();

            $table->longText('body')->nullable();

            // text=0, image=1, video=2, audio=3, file=4, voice_note=5, system=6, gif=7
            $table->unsignedTinyInteger('message_type')->default(0);

            $table->json('metadata')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['conversation_id', 'id']);
            $table->index(['conversation_id', 'sent_at']);
            $table->index('sender_id');
            $table->index('reply_to_id');
            $table->index('parent_message_id');
            $table->index('forwarded_from_id');
            $table->index('message_type');

            // FULLTEXT is not supported on SQLite (local/testing default).
            if (in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb', 'pgsql'], true)) {
                $table->fullText('body');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
