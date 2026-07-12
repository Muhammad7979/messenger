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

            // Primary Key
            $table->id();

            // Public Identifier
            $table->char('uuid', 36)->unique();

            // Conversation
            $table->foreignId('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();

            // Sender
            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Thread Parent Message
            $table->foreignId('parent_message_id')
                ->nullable()
                ->constrained('messages')
                ->nullOnDelete();

            // Reply Message
            $table->foreignId('reply_to_id')
                ->nullable()
                ->constrained('messages')
                ->nullOnDelete();

            // Forwarded Message
            $table->foreignId('forwarded_from_id')
                ->nullable()
                ->constrained('messages')
                ->nullOnDelete();

            // Message Body
            $table->longText('body')->nullable();

            // Message Type
            // $table->enum('message_type', [
            //     'text',
            //     'image',
            //     'video',
            //     'audio',
            //     'file',
            //     'voice_note',
            //     'system',
            // ])->default('text');

            $table->unsignedTinyInteger('message_type');

            // Extra Information
            $table->json('metadata')->nullable();

            // Timestamps
            $table->timestamp('sent_at')->nullable();

            $table->timestamp('edited_at')->nullable();

            $table->softDeletes(); // deleted_at

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(['conversation_id', 'id']);

            $table->index(['conversation_id', 'sent_at']);

            $table->index('sender_id');

            $table->index('reply_to_id');

            $table->index('parent_message_id');

            $table->index('forwarded_from_id');

            /*
            |--------------------------------------------------------------------------
            | Full Text Search
            |--------------------------------------------------------------------------
            */

            $table->fullText('body');
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