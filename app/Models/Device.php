<?php

namespace App\Models;

use App\Enums\DevicePlatform;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use HasFactory, HasUuids;

    public function uniqueIds(): array
    {
        return ['device_uuid'];
    }

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

    protected function casts(): array
    {
        return [
            'platform' => DevicePlatform::class,
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function presence(): HasOne
    {
        return $this->hasOne(UserPresence::class);
    }

    public function typingStatuses(): HasMany
    {
        return $this->hasMany(TypingStatus::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(DeviceSession::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isMobile(): bool
    {
        return $this->platform?->isMobile() ?? false;
    }

    public function isWeb(): bool
    {
        return $this->platform === DevicePlatform::Web;
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
}
