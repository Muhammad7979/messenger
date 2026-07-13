<?php

namespace App\Models;

use App\Enums\ConversationType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'uuid',
        'type',
        'created_by',
        'name',
        'last_message_id',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected function casts(): array
    {
        return [
            'type' => ConversationType::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ConversationMember::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    public function typingStatuses(): HasMany
    {
        return $this->hasMany(TypingStatus::class);
    }

    public function pins(): HasMany
    {
        return $this->hasMany(MessagePin::class);
    }

    public function scopePrivate($query)
    {
        return $query->where('type', ConversationType::Private);
    }

    public function scopeGroup($query)
    {
        return $query->where('type', ConversationType::Group);
    }

    public function scopeChannel($query)
    {
        return $query->where('type', ConversationType::Channel);
    }

    public function isPrivate(): bool
    {
        return $this->type === ConversationType::Private;
    }

    public function isGroup(): bool
    {
        return $this->type === ConversationType::Group;
    }

    public function isChannel(): bool
    {
        return $this->type === ConversationType::Channel;
    }
}
