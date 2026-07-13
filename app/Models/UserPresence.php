<?php

namespace App\Models;

use App\Enums\PresenceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPresence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'status',
        'socket_id',
        'last_seen',
    ];

    protected function casts(): array
    {
        return [
            'status' => PresenceStatus::class,
            'last_seen' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeOnline($query)
    {
        return $query->where('status', PresenceStatus::Online);
    }

    public function scopeOffline($query)
    {
        return $query->where('status', PresenceStatus::Offline);
    }

    public function isOnline(): bool
    {
        return $this->status === PresenceStatus::Online;
    }

    public function isOffline(): bool
    {
        return $this->status === PresenceStatus::Offline;
    }

    public function markOnline(?string $socketId = null): void
    {
        $this->update([
            'status' => PresenceStatus::Online,
            'socket_id' => $socketId,
            'last_seen' => now(),
        ]);
    }

    public function markOffline(): void
    {
        $this->update([
            'status' => PresenceStatus::Offline,
            'socket_id' => null,
            'last_seen' => now(),
        ]);
    }
}
