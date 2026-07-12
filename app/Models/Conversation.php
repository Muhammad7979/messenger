<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory, HasUuids;

    /**
     * Conversation Types
     */
    public const TYPE_PRIVATE = 'private';
    public const TYPE_GROUP = 'group';
    public const TYPE_CHANNEL = 'channel';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'created_by',
        'name',
        'last_message_id',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Route Model Binding uses UUID.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Conversation Creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Members of Conversation
     */
    public function members(): HasMany
    {
        return $this->hasMany(ConversationMember::class);
    }

    /**
     * Messages
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Last Message
     */
    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePrivate($query)
    {
        return $query->where('type', self::TYPE_PRIVATE);
    }

    public function scopeGroup($query)
    {
        return $query->where('type', self::TYPE_GROUP);
    }

    public function scopeChannel($query)
    {
        return $query->where('type', self::TYPE_CHANNEL);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isPrivate(): bool
    {
        return $this->type === self::TYPE_PRIVATE;
    }

    public function isGroup(): bool
    {
        return $this->type === self::TYPE_GROUP;
    }

    public function isChannel(): bool
    {
        return $this->type === self::TYPE_CHANNEL;
    }

    public function typingStatuses(): HasMany
    {
        return $this->hasMany(TypingStatus::class);
    }
}