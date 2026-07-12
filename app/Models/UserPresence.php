<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPresence extends Model
{
    use HasFactory;

    public const STATUS_ONLINE  = 'online';
    public const STATUS_OFFLINE = 'offline';
    public const STATUS_AWAY    = 'away';
    public const STATUS_BUSY    = 'busy';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'user_id',
        'device_id',
        'status',
        'socket_id',
        'last_seen',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'last_seen' => 'datetime',
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
     * User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Device.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOnline($query)
    {
        return $query->where('status', self::STATUS_ONLINE);
    }

    public function scopeOffline($query)
    {
        return $query->where('status', self::STATUS_OFFLINE);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isOnline(): bool
    {
        return $this->status === self::STATUS_ONLINE;
    }

    public function isOffline(): bool
    {
        return $this->status === self::STATUS_OFFLINE;
    }

    public function markOnline(?string $socketId = null): void
    {
        $this->update([
            'status' => self::STATUS_ONLINE,
            'socket_id' => $socketId,
            'last_seen' => now(),
        ]);
    }

    public function markOffline(): void
    {
        $this->update([
            'status' => self::STATUS_OFFLINE,
            'socket_id' => null,
            'last_seen' => now(),
        ]);
    }
}