<?php

namespace App\Models;

use App\Enums\MemberRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'role',
        'joined_at',
        'left_at',
        'last_read_message_id',
        'is_muted',
        'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'role' => MemberRole::class,
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
            'is_muted' => 'boolean',
            'is_archived' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastReadMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_read_message_id');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('left_at');
    }

    public function scopeMuted($query)
    {
        return $query->where('is_muted', true);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('role', [
            MemberRole::Owner,
            MemberRole::Admin,
        ]);
    }

    public function isOwner(): bool
    {
        return $this->role === MemberRole::Owner;
    }

    public function isAdmin(): bool
    {
        return $this->role?->isPrivileged() ?? false;
    }

    public function isModerator(): bool
    {
        return $this->role === MemberRole::Moderator;
    }

    public function hasLeft(): bool
    {
        return ! is_null($this->left_at);
    }

    public function isMuted(): bool
    {
        return $this->is_muted;
    }

    public function isArchived(): bool
    {
        return $this->is_archived;
    }
}
