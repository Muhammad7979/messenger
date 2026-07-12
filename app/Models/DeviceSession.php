<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceSession extends Model
{
    use HasFactory;

    /**
     * The underlying table.
     */
    protected $table = 'sessions';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'device_id',
        'token_hash',
        'ip_address',
        'user_agent',
        'last_activity_at',
        'expires_at',
        'revoked_at',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'last_activity_at' => 'datetime',
            'expires_at'       => 'datetime',
            'revoked_at'       => 'datetime',
            'created_at'       => 'datetime',
            'updated_at'       => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
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

    public function scopeActive($query)
    {
        return $query
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return ! is_null($this->revoked_at);
    }

    public function isActive(): bool
    {
        return ! $this->isExpired() && ! $this->isRevoked();
    }

    public function revoke(): void
    {
        $this->update([
            'revoked_at' => now(),
        ]);
    }

    public function touchActivity(): void
    {
        $this->update([
            'last_activity_at' => now(),
        ]);
    }
}