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
        Schema::create('conversation_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // owner, admin, moderator, member
            $table->string('role', 30)->default('member');

            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();

            // Denormalized read cursor; FK added after messages table exists.
            $table->unsignedBigInteger('last_read_message_id')->nullable();

            $table->boolean('is_muted')->default(false);
            $table->boolean('is_archived')->default(false);

            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);

            $table->index('role');
            $table->index('joined_at');
            $table->index(['user_id', 'is_archived']);
            $table->index(['conversation_id', 'last_read_message_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_members');
    }
};
