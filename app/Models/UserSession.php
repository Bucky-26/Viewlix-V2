<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'device_type',
        'browser',
        'os',
        'user_agent',
        'login_at',
        'last_activity_at',
        'logout_at',
        'is_active',
        'country',
        'city',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'logout_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns this session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get active sessions only
     */
    public static function active()
    {
        return self::where('is_active', true);
    }

    /**
     * Mark session as inactive (logout)
     */
    public function logout()
    {
        $this->update([
            'is_active' => false,
            'logout_at' => now(),
        ]);
    }

    /**
     * Update last activity timestamp
     */
    public function updateActivity()
    {
        $this->update([
            'last_activity_at' => now(),
        ]);
    }
}
