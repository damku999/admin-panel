<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Crypt;

/**
 * App\Models\TwoFactorAuth
 *
 * @method static \Database\Factories\TwoFactorAuthFactory factory($count = null, $state = [])
 */
class TwoFactorAuth extends Model
{
    use HasFactory;

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
     * Get the authenticatable entity (User or Customer)
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Encrypt/decrypt the secret when storing/retrieving
     */
    protected function secret(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Crypt::decryptString($value) : null,
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
     * Check if 2FA is fully configured and active
     */
    public function isFullyConfigured(): bool
    {
        return $this->is_active &&
               $this->secret &&
               $this->confirmed_at &&
               $this->recovery_codes;
    }

    /**
     * Check if 2FA setup is pending confirmation
     */
    public function isPendingConfirmation(): bool
    {
        return $this->secret &&
               $this->enabled_at &&
               ! $this->confirmed_at;
    }

    /**
     * Generate new recovery codes
     */
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }

        $this->recovery_codes = $codes;
        $this->save();

        return $codes;
    }

    /**
     * Use a recovery code (mark as used)
     */
    public function useRecoveryCode(string $code): bool
    {
        $codes = $this->recovery_codes;
        $upperCode = strtoupper($code);

        // Debug logging
        \Log::debug('Recovery code verification attempt', [
            'input_code' => $code,
            'upper_code' => $upperCode,
            'stored_codes' => $codes,
            'codes_exist' => ! empty($codes),
            'code_in_array' => $codes ? in_array($upperCode, $codes) : false,
        ]);

        if (! $codes || ! in_array($upperCode, $codes)) {
            \Log::warning('Recovery code verification failed', [
                'input_code' => $code,
                'codes_exist' => ! empty($codes),
                'available_codes_count' => $codes ? count($codes) : 0,
            ]);

            return false;
        }

        // Remove the used code
        $codes = array_filter($codes, fn ($c) => $c !== $upperCode);
        $this->recovery_codes = array_values($codes);
        $this->save();

        \Log::info('Recovery code used successfully', [
            'used_code' => $upperCode,
            'remaining_codes' => count($this->recovery_codes),
        ]);

        return true;
    }

    /**
     * Check if recovery code is valid
     */
    public function isValidRecoveryCode(string $code): bool
    {
        $codes = $this->recovery_codes;

        return $codes && in_array(strtoupper($code), $codes);
    }

    /**
     * Get remaining recovery codes count
     */
    public function getRemainingRecoveryCodesCount(): int
    {
        return $this->recovery_codes ? count($this->recovery_codes) : 0;
    }

    /**
     * Disable 2FA
     */
    public function disable(): void
    {
        $this->update([
            'is_active' => false,
            'secret' => null,
            'recovery_codes' => null,
            'confirmed_at' => null,
        ]);
    }

    /**
     * Confirm 2FA setup
     */
    public function confirm(): void
    {
        $this->update([
            'confirmed_at' => now(),
            'is_active' => true,
        ]);
    }
}
