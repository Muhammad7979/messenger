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

            // Conversation Type
            // $table->enum('type', [
            //     'private',
            //     'group',
            //     'channel',
            // ]);

            $table->tinyInteger('type');

            // User who created the conversation
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // Conversation Name
            // Nullable because private chats usually don't have a name.
            $table->string('name')->nullable();

            // Last Message ID (nullable until first message is sent)
            $table->unsignedBigInteger('last_message_id')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('type');
            $table->index('created_by');

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */

            $table->foreign('last_message_id')
                ->references('id')
                ->on('messages')
                ->nullOnDelete();
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