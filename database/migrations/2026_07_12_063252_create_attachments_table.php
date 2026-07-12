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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            $table->foreignId('message_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Storage Information
            |--------------------------------------------------------------------------
            */

            // local, s3, minio
            $table->string('storage', 30);

            // uploads/messages/2026/06/file.jpg
            $table->string('path');

            /*
            |--------------------------------------------------------------------------
            | File Information
            |--------------------------------------------------------------------------
            */

            $table->string('original_name');

            $table->string('mime_type', 100);

            // bytes
            $table->unsignedBigInteger('size');

            /*
            |--------------------------------------------------------------------------
            | Media Metadata
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('width')->nullable();

            $table->unsignedInteger('height')->nullable();

            // seconds (voice/video)
            $table->unsignedInteger('duration')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Integrity
            |--------------------------------------------------------------------------
            */

            $table->string('checksum', 64)->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('message_id');
            $table->index('mime_type');
            $table->index('storage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};