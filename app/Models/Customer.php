<?php

namespace App\Models;

use App\Models\CustomerInsurance;
use App\Models\Quotation;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * App\Models\Customer
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $mobile_number
 * @property string|null $date_of_birth
 * @property string|null $wedding_anniversary_date
 * @property string|null $engagement_anniversary_date
 * @property string|null $type
 * @property int|null $status
 * @property string|null $pan_card_number
 * @property string|null $aadhar_card_number
 * @property string|null $gst_number
 * @property string|null $pan_card_path
 * @property string|null $aadhar_card_path
 * @property string|null $gst_path
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerInsurance> $insurance
 * @property-read int|null $insurance_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAadharCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAadharCardPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereEngagementAnniversaryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereGstNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereGstPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePanCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePanCardPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereWeddingAnniversaryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withoutTrashed()
 * @mixin \Eloquent
 */
class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile_number',
        'status',
        'wedding_anniversary_date',
        'date_of_birth',
        'engagement_anniversary_date',
        'pan_card_number',
        'aadhar_card_number',
        'gst_number',
        'pan_card_path',
        'aadhar_card_path',
        'gst_path',
        'type',
        'family_group_id',
        'password',
        'email_verified_at',
        'password_changed_at',
        'must_change_password',
        'email_verification_token',
        'password_reset_sent_at',
        'password_reset_token',
        'password_reset_expires_at'
    ];

    protected $casts = [
        'status' => 'boolean',
        'date_of_birth' => 'date',
        'wedding_anniversary_date' => 'date',
        'engagement_anniversary_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'must_change_password' => 'boolean',
        'password_reset_sent_at' => 'datetime',
        'password_reset_expires_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Override the boot method to handle customer guard authentication
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // Check both customer and web guards for created_by
            if (Auth::guard('customer')->check()) {
                $model->created_by = Auth::guard('customer')->id();
                $model->updated_by = Auth::guard('customer')->id();
            } elseif (Auth::guard('web')->check()) {
                $model->created_by = Auth::guard('web')->id();
                $model->updated_by = Auth::guard('web')->id();
            } else {
                $model->created_by = 0;
                $model->updated_by = 0;
            }
        });

        static::updating(function ($model) {
            // Check both customer and web guards for updated_by
            if (Auth::guard('customer')->check()) {
                $model->updated_by = Auth::guard('customer')->id();
            } elseif (Auth::guard('web')->check()) {
                $model->updated_by = Auth::guard('web')->id();
            } else {
                $model->updated_by = 0;
            }
        });

        static::deleting(function ($model) {
            // Check both customer and web guards for deleted_by
            if (Auth::guard('customer')->check()) {
                $model->deleted_by = Auth::guard('customer')->id();
            } elseif (Auth::guard('web')->check()) {
                $model->deleted_by = Auth::guard('web')->id();
            } else {
                $model->deleted_by = 0;
            }
            $model->save();
        });
    }

    /**
     * Get the insurance for the customer.
     */
    public function insurance(): HasMany
    {
        return $this->hasMany(CustomerInsurance::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    /**
     * Get the family group this customer belongs to.
     */
    public function familyGroup(): BelongsTo
    {
        return $this->belongsTo(FamilyGroup::class);
    }

    /**
     * Get the family member record for this customer.
     */
    public function familyMember(): HasOne
    {
        return $this->hasOne(FamilyMember::class);
    }

    /**
     * Get all family members if this customer is part of a family.
     */
    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class, 'family_group_id', 'family_group_id');
    }

    /**
     * Get all family insurance policies if this customer is part of a family.
     */
    public function familyInsurance(): HasMany
    {
        // SECURITY FIX: Validate family_group_id to prevent SQL injection
        $familyGroupId = $this->validateFamilyGroupId($this->family_group_id);
        
        return $this->hasMany(CustomerInsurance::class, 'customer_id')
            ->whereHas('customer', function ($query) use ($familyGroupId) {
                $query->where('family_group_id', '=', $familyGroupId);
            });
    }

    /**
     * Check if customer is part of a family group.
     */
    public function hasFamily(): bool
    {
        return !is_null($this->family_group_id);
    }

    /**
     * Check if customer is the family head.
     */
    public function isFamilyHead(): bool
    {
        if (!$this->hasFamily()) {
            return false;
        }
        
        return $this->familyMember?->is_head === true;
    }

    /**
     * Get all insurance policies this customer can view (own + family if head).
     */
    public function getViewableInsurance()
    {
        if ($this->isFamilyHead()) {
            // SECURITY FIX: Validate family_group_id to prevent SQL injection
            $familyGroupId = $this->validateFamilyGroupId($this->family_group_id);
            
            // Family head can view all family insurance
            return CustomerInsurance::whereHas('customer', function ($query) use ($familyGroupId) {
                $query->where('family_group_id', '=', $familyGroupId);
            })->with(['customer', 'insuranceCompany', 'policyType', 'premiumType']);
        } else {
            // Regular members can only view their own insurance
            return $this->insurance()->with(['insuranceCompany', 'policyType', 'premiumType']);
        }
    }

    /**
     * Check if this customer is in the same family as another customer.
     */
    public function isInSameFamilyAs(Customer $customer): bool
    {
        return $this->hasFamily() && 
               $customer->hasFamily() && 
               $this->family_group_id === $customer->family_group_id;
    }

    /**
     * Get privacy-safe customer data for family viewing.
     */
    public function getPrivacySafeData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->maskEmail($this->email),
            'mobile_number' => $this->maskMobile($this->mobile_number),
            'date_of_birth' => $this->date_of_birth?->format('M d'), // Hide year for privacy
            'status' => $this->status,
            'created_at' => $this->created_at->format('M Y'),
            'relationship' => $this->familyMember?->relationship
        ];
    }

    /**
     * Mask email for privacy (show first 2 chars and domain).
     */
    protected function maskEmail(?string $email): ?string
    {
        if (!$email) return null;
        
        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;
        
        $username = $parts[0];
        $domain = $parts[1];
        
        if (strlen($username) <= 2) {
            return $username . '@' . $domain;
        }
        
        return substr($username, 0, 2) . str_repeat('*', strlen($username) - 2) . '@' . $domain;
    }

    /**
     * Mask mobile number for privacy.
     */
    protected function maskMobile(?string $mobile): ?string
    {
        if (!$mobile || strlen($mobile) < 4) return $mobile;
        
        return substr($mobile, 0, 2) . str_repeat('*', strlen($mobile) - 4) . substr($mobile, -2);
    }

    /**
     * Check if customer can view sensitive data of another customer.
     */
    public function canViewSensitiveDataOf(Customer $customer): bool
    {
        // Can always view own data
        if ($this->id === $customer->id) {
            return true;
        }
        
        // Family head can view family members' data
        return $this->isFamilyHead() && $this->isInSameFamilyAs($customer);
    }

    /**
     * Get audit log for this customer.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(CustomerAuditLog::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function isActive(): bool
    {
        return $this->status === true;
    }

    public function isRetailCustomer(): bool
    {
        return $this->type === 'Retail';
    }

    public function isCorporateCustomer(): bool
    {
        return $this->type === 'Corporate';
    }

    protected function panCardPath(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? asset('storage/' . $value) : null,
        );
    }

    protected function aadharCardPath(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? asset('storage/' . $value) : null,
        );
    }

    protected function gstPath(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? asset('storage/' . $value) : null,
        );
    }

    /**
     * Generate a random password for the customer.
     */
    public static function generateDefaultPassword(): string
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
    }

    /**
     * Set a password and mark it for required change.
     */
    public function setDefaultPassword(?string $password = null): string
    {
        $plainPassword = $password ?? self::generateDefaultPassword();
        
        $this->update([
            'password' => Hash::make($plainPassword),
            'must_change_password' => true,
            'password_changed_at' => null,
            'email_verified_at' => null,
            'email_verification_token' => Str::random(60),
        ]);

        return $plainPassword;
    }

    /**
     * Set a custom password with admin control over password change requirement.
     */
    public function setCustomPassword(string $plainPassword, bool $forceChange = true): string
    {
        $this->update([
            'password' => Hash::make($plainPassword),
            'must_change_password' => $forceChange,
            'password_changed_at' => $forceChange ? null : now(),
            'email_verified_at' => null,
            'email_verification_token' => Str::random(60),
        ]);

        return $plainPassword;
    }

    /**
     * Change password and mark as user-changed.
     */
    public function changePassword(string $newPassword): void
    {
        $this->update([
            'password' => Hash::make($newPassword),
            'must_change_password' => false,
            'password_changed_at' => now(),
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);
    }

    /**
     * Check if customer needs to change password.
     */
    public function needsPasswordChange(): bool
    {
        return (bool) $this->must_change_password;
    }

    /**
     * Check if customer's email is verified.
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Generate email verification token.
     */
    public function generateEmailVerificationToken(): string
    {
        $token = Str::random(60);
        $this->update(['email_verification_token' => $token]);
        return $token;
    }

    /**
     * Verify email with token.
     */
    public function verifyEmail(string $token): bool
    {
        if ($this->email_verification_token === $token) {
            $this->update([
                'email_verified_at' => now(),
                'email_verification_token' => null,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Generate secure password reset token with expiration.
     */
    public function generatePasswordResetToken(): string
    {
        // Generate cryptographically secure token with higher entropy
        $token = bin2hex(random_bytes(32)); // 64 character hex string
        
        // Set expiration to 1 hour from now
        $expiresAt = now()->addHour();
        
        $this->update([
            'password_reset_token' => $token,
            'password_reset_expires_at' => $expiresAt,
            'password_reset_sent_at' => now()
        ]);
        
        return $token;
    }

    /**
     * Verify password reset token and check expiration.
     */
    public function verifyPasswordResetToken(string $token): bool
    {
        if (!$this->password_reset_token || !$this->password_reset_expires_at) {
            return false;
        }

        // Check if token matches
        if (!hash_equals($this->password_reset_token, $token)) {
            return false;
        }

        // Check if token has expired
        if (now()->isAfter($this->password_reset_expires_at)) {
            // Clear expired token
            $this->update([
                'password_reset_token' => null,
                'password_reset_expires_at' => null
            ]);
            return false;
        }

        return true;
    }

    /**
     * Clear password reset token after successful reset.
     */
    public function clearPasswordResetToken(): void
    {
        $this->update([
            'password_reset_token' => null,
            'password_reset_expires_at' => null
        ]);
    }

    /**
     * Mask PAN number for customer portal display (show first 3 and last 1 characters).
     * Example: CFDPB1228P -> CFD*****8P
     */
    public function getMaskedPanNumber(): ?string
    {
        if (!$this->pan_card_number) {
            return null;
        }
        
        $pan = $this->pan_card_number;
        $length = strlen($pan);
        
        if ($length < 4) {
            return str_repeat('*', $length);
        }
        
        // Show first 3 characters + stars + last 1 character
        return substr($pan, 0, 3) . str_repeat('*', $length - 4) . substr($pan, -1);
    }

    /**
     * Validate and sanitize family group ID to prevent SQL injection.
     */
    protected function validateFamilyGroupId($familyGroupId)
    {
        // Check if family group ID is null
        if (is_null($familyGroupId)) {
            throw new \InvalidArgumentException('Family group ID cannot be null for family operations');
        }

        // Ensure it's an integer to prevent SQL injection
        if (!is_numeric($familyGroupId)) {
            throw new \InvalidArgumentException('Family group ID must be numeric');
        }

        $familyGroupId = (int) $familyGroupId;

        // Validate that it's a positive integer
        if ($familyGroupId <= 0) {
            throw new \InvalidArgumentException('Family group ID must be a positive integer');
        }

        // Additional security: Verify the family group actually exists and is active
        $familyGroupExists = \DB::table('family_groups')
            ->where('id', '=', $familyGroupId)
            ->where('status', '=', true)
            ->exists();

        if (!$familyGroupExists) {
            throw new \InvalidArgumentException('Invalid or inactive family group ID');
        }

        return $familyGroupId;
    }
}
