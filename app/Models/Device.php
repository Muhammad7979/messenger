<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use HasFactory, HasUuids;

    /**
     * Platforms
     */
    public const PLATFORM_ANDROID = 'android';
    public const PLATFORM_IOS = 'ios';
    public const PLATFORM_WEB = 'web';
    public const PLATFORM_WINDOWS = 'windows';
    public const PLATFORM_MACOS = 'macos';
    public const PLATFORM_LINUX = 'linux';

    /**
     * UUID column.
     */
    public function uniqueIds(): array
    {
        return ['device_uuid'];
    }

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'user_id',
        'device_uuid',
        'platform',
        'device_name',
        'device_model',
        'os_version',
        'app_version',
        'push_token',
        'ip_address',
        'last_login_at',
        'last_seen_at',
        'is_active',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function presence()
    {
        return $this->hasOne(UserPresence::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isMobile(): bool
    {
        return in_array($this->platform, [
            self::PLATFORM_ANDROID,
            self::PLATFORM_IOS,
        ], true);
    }

    public function isWeb(): bool
    {
        return $this->platform === self::PLATFORM_WEB;
    }

    public function markOnline(): void
    {
        $this->update([
            'last_seen_at' => now(),
        ]);
    }

    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
        ]);
    }

    public function typingStatuses(): HasMany
    {
        return $this->hasMany(TypingStatus::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(DeviceSession::class, 'device_id');
    }


}