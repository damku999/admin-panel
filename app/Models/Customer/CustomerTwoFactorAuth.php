<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Customer;
use Carbon\Carbon;

/**
 * Customer-specific Two Factor Authentication Model
 * Separate from admin 2FA to prevent conflicts
 */
class CustomerTwoFactorAuth extends Model
{
    protected $table = 'two_factor_auth';

    protected $fillable = [
        'authenticatable_type',
        'authenticatable_id',
        'secret',
        'recovery_codes',
        'enabled_at',
        'confirmed_at',
        'is_active',
        'backup_method',
        'backup_destination',
    ];

    protected $casts = [
        'recovery_codes' => 'array',
        'enabled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the authenticatable model (Customer only)
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to only customer records
     */
    public function scopeCustomersOnly($query)
    {
        return $query->where('authenticatable_type', Customer::class);
    }

    /**
     * Check if 2FA is fully configured
     */
    public function isFullyConfigured(): bool
    {
        return !empty($this->secret) &&
               !empty($this->recovery_codes) &&
               $this->confirmed_at !== null &&
               $this->is_active;
    }

    /**
     * Check if setup is pending confirmation
     */
    public function isPendingConfirmation(): bool
    {
        return !empty($this->secret) &&
               !empty($this->recovery_codes) &&
               $this->confirmed_at === null &&
               $this->is_active;
    }

    /**
     * Generate new recovery codes
     */
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(5)));
        }

        $this->recovery_codes = $codes;
        $this->save();

        return $codes;
    }

    /**
     * Get remaining recovery codes count
     */
    public function getRemainingRecoveryCodesCount(): int
    {
        return is_array($this->recovery_codes) ? count($this->recovery_codes) : 0;
    }

    /**
     * Use a recovery code
     */
    public function useRecoveryCode(string $code): bool
    {
        if (!is_array($this->recovery_codes)) {
            return false;
        }

        $codeIndex = array_search(strtoupper($code), $this->recovery_codes);
        if ($codeIndex !== false) {
            $codes = $this->recovery_codes;
            unset($codes[$codeIndex]);
            $this->recovery_codes = array_values($codes);
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Mark as confirmed
     */
    public function confirm(): void
    {
        $this->confirmed_at = now();
        $this->is_active = true;
        $this->save();
    }

    /**
     * Disable 2FA
     */
    public function disable(): void
    {
        $this->is_active = false;
        $this->save();
    }
}