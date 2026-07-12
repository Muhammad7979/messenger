<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    /**
     * Storage Drivers
     */
    public const STORAGE_LOCAL = 'local';
    public const STORAGE_S3 = 's3';
    public const STORAGE_MINIO = 'minio';

    /**
     * Fillable Attributes
     */
    protected $fillable = [
        'message_id',
        'storage',
        'path',
        'original_name',
        'mime_type',
        'size',
        'width',
        'height',
        'duration',
        'checksum',
    ];

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'duration' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Parent Message
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get the public URL of the attachment.
     */
    public function getUrl(): string
    {
        return Storage::disk($this->storage)->url($this->path);
    }

    /**
     * Check if the attachment is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if the attachment is a video.
     */
    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    /**
     * Check if the attachment is an audio file.
     */
    public function isAudio(): bool
    {
        return str_starts_with($this->mime_type, 'audio/');
    }

    /**
     * Check if the attachment is a PDF.
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Check if the attachment is a document.
     */
    public function isDocument(): bool
    {
        return ! (
            $this->isImage() ||
            $this->isVideo() ||
            $this->isAudio()
        );
    }

    /**
     * Human-readable file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}