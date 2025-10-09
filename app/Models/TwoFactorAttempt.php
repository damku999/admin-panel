<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\TwoFactorAttempt
 *
 * @property int $id
 * @property string $authenticatable_type
 * @property int $authenticatable_id
 * @property string $code_type
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property bool $successful
 * @property string|null $failure_reason
 * @property \Illuminate\Support\Carbon $attempted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $authenticatable
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt codeType(string $codeType)
 * @method static \Database\Factories\TwoFactorAttemptFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt failed()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt recent(int $minutes = 15)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt successful()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt whereAttemptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt whereAuthenticatableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt whereAuthenticatableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt whereCodeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt whereSuccessful($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorAttempt whereUserAgent($value)
 * @mixin \Eloquent
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
        return match ($this->code_type) {
            'totp' => 'Authenticator App',
            'recovery' => 'Recovery Code',
            'sms' => 'SMS',
            default => ucfirst($this->code_type)
        };
    }
}
