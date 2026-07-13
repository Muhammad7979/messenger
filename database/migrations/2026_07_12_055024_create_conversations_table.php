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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            // Public UUID
            $table->uuid('uuid')->unique();

            // Conversation Type: private=1, group=2, channel=3
            $table->unsignedTinyInteger('type');

            // User who created the conversation (restrict so history is preserved)
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            // Nullable because private chats usually don't have a name.
            $table->string('name')->nullable();

            // Denormalized pointer; FK added after messages table exists.
            $table->unsignedBigInteger('last_message_id')->nullable();

            $table->timestamps();

            $table->index('type');
            $table->index('created_by');
            $table->index('last_message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
