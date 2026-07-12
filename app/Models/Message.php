<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'conversation_id',
        'sender_id',
        'parent_message_id',
        'reply_to_id',
        'forwarded_from_id',
        'body',
        'message_type',
        'metadata',
        'sent_at',
        'edited_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function parentMessage()
    {
        return $this->belongsTo(Message::class, 'parent_message_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function forwardedFrom()
    {
        return $this->belongsTo(Message::class, 'forwarded_from_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }
}