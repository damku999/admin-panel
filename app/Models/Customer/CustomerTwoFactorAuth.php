<?php

namespace App\Models\Customer;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Crypt;

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

    protected $hidden = [
        'secret',
        'recovery_codes',
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
     * Encrypt/decrypt the secret when storing/retrieving
     */
    protected function secret(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! $value) {
                    return null;
                }

                // Handle legacy unencrypted secrets (backwards compatibility)
                try {
                    return Crypt::decryptString($value);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    // If decryption fails, assume it's an old unencrypted secret
                    \Log::warning('Found legacy unencrypted customer 2FA secret', [
                        'customer_id' => $this->authenticatable_id,
                        'secret_preview' => substr($value, 0, 8).'...',
                    ]);

                    return $value;
                }
            },
            set: fn ($value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * Encrypt/decrypt recovery codes when storing/retrieving
     */
    protected function recoveryCodes(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! $value) {
                    return null;
                }
                $codes = json_decode($value, true);

                return array_map(fn ($code) => Crypt::decryptString($code), $codes);
            },
            set: function ($value) {
                if (! $value) {
                    return null;
                }
                $encryptedCodes = array_map(fn ($code) => Crypt::encryptString($code), $value);

                return json_encode($encryptedCodes);
            },
        );
    }

    /**
     * Check if 2FA is fully configured
     */
    public function isFullyConfigured(): bool
    {
        return ! empty($this->secret) &&
               ! empty($this->recovery_codes) &&
               $this->confirmed_at !== null &&
               $this->is_active;
    }

    /**
     * Check if setup is pending confirmation
     */
    public function isPendingConfirmation(): bool
    {
        return ! empty($this->secret) &&
               ! empty($this->recovery_codes) &&
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
            // Generate 8-character alphanumeric codes for customers
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
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
        $upperCode = strtoupper($code);

        // Debug logging for customer recovery codes
        \Log::debug('Customer recovery code verification attempt', [
            'input_code' => $code,
            'upper_code' => $upperCode,
            'stored_codes' => $this->recovery_codes,
            'codes_exist' => is_array($this->recovery_codes),
            'code_in_array' => is_array($this->recovery_codes) ? in_array($upperCode, $this->recovery_codes) : false,
        ]);

        if (! is_array($this->recovery_codes)) {
            \Log::warning('Customer recovery code verification failed - no codes stored', [
                'input_code' => $code,
                'recovery_codes_type' => gettype($this->recovery_codes),
            ]);

            return false;
        }

        $codeIndex = array_search($upperCode, $this->recovery_codes);
        if ($codeIndex !== false) {
            $codes = $this->recovery_codes;
            unset($codes[$codeIndex]);
            $this->recovery_codes = array_values($codes);
            $this->save();

            \Log::info('Customer recovery code used successfully', [
                'used_code' => $upperCode,
                'remaining_codes' => count($this->recovery_codes),
            ]);

            return true;
        }

        \Log::warning('Customer recovery code verification failed', [
            'input_code' => $code,
            'available_codes_count' => count($this->recovery_codes),
        ]);

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
