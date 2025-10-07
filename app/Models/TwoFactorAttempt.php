<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\TwoFactorAttempt
 *
 * @method static \Database\Factories\TwoFactorAttemptFactory factory($count = null, $state = [])
 */
class TwoFactorAttempt extends Model
{
    use HasFactory;
    protected $fillable = [
        'authenticatable_type',
        'authenticatable_id',
        'code_type',
        'ip_address',
        'user_agent',
        'successful',
        'failure_reason',
        'attempted_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    /**
     * Get the authenticatable entity (User or Customer)
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for successful attempts
     */
    public function scopeSuccessful($query)
    {
        return $query->where('successful', true);
    }

    /**
     * Scope for failed attempts
     */
    public function scopeFailed($query)
    {
        return $query->where('successful', false);
    }

    /**
     * Scope for recent attempts
     */
    public function scopeRecent($query, int $minutes = 15)
    {
        return $query->where('attempted_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Scope for specific code type
     */
    public function scopeCodeType($query, string $codeType)
    {
        return $query->where('code_type', $codeType);
    }

    /**
     * Get attempt result display
     */
    public function getResultDisplay(): string
    {
        return $this->successful ? 'Success' : 'Failed';
    }

    /**
     * Get code type display
     */
    public function getCodeTypeDisplay(): string
    {
        return match($this->code_type) {
            'totp' => 'Authenticator App',
            'recovery' => 'Recovery Code',
            'sms' => 'SMS',
            default => ucfirst($this->code_type)
        };
    }
}