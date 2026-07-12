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

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            $table->foreignId('conversation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Member Information
            |--------------------------------------------------------------------------
            */

            $table->string('role', 30)->default('member');
            // member, admin, moderator, owner

            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Read Tracking
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('last_read_message_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | User Preferences
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_muted')->default(false);
            $table->boolean('is_archived')->default(false);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Constraints
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'conversation_id',
                'user_id'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('user_id');
            $table->index('conversation_id');
            $table->index('role');
            $table->index('joined_at');

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign('last_read_message_id')
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
        Schema::dropIfExists('conversation_members');
    }
};