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
        Schema::create('devices', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Device Information
            |--------------------------------------------------------------------------
            */

            $table->uuid('device_uuid')->unique();

            $table->enum('platform', [
                'android',
                'ios',
                'web',
                'windows',
                'macos',
                'linux',
            ]);

            $table->string('device_name');

            $table->string('device_model')->nullable();

            $table->string('os_version')->nullable();

            $table->string('app_version')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Push Notifications
            |--------------------------------------------------------------------------
            */

            $table->text('push_token')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Security
            |--------------------------------------------------------------------------
            */

            $table->ipAddress('ip_address')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Activity
            |--------------------------------------------------------------------------
            */

            $table->timestamp('last_login_at')->nullable();

            $table->timestamp('last_seen_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('user_id');
            $table->index('platform');
            $table->index('last_seen_at');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};