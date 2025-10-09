<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\CustomerAuditLog
 *
 * @property int $id
 * @property int $customer_id
 * @property string $action login, logout, view_policy, download_document, etc.
 * @property string|null $resource_type policy, profile, family_data
 * @property int|null $resource_id ID of the resource being accessed
 * @property string|null $description Human readable description
 * @property array|null $metadata Additional data (JSON-like string, IP, user agent, etc.)
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $session_id
 * @property bool $success
 * @property string|null $failure_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer|null $customer
 * @method static \Database\Factories\CustomerAuditLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereResourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerAuditLog whereUserAgent($value)
 * @mixin \Eloquent
 */
class CustomerAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'action',
        'resource_type',
        'resource_id',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'session_id',
        'success',
        'failure_reason',
    ];

    protected $casts = [
        'metadata' => 'array',
        'success' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public static function logAction(string $action, ?string $description = null, array $metadata = []): void
    {
        $customer = auth('customer')->user();

        if (! $customer) {
            return;
        }

        self::create([
            'customer_id' => $customer->id,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'success' => true,
        ]);
    }

    public static function logPolicyAction(string $action, CustomerInsurance $policy, ?string $description = null, array $metadata = []): void
    {
        $customer = auth('customer')->user();

        if (! $customer) {
            return;
        }

        self::create([
            'customer_id' => $customer->id,
            'action' => $action,
            'resource_type' => 'policy',
            'resource_id' => $policy->id,
            'description' => $description ?: "Customer {$action} policy {$policy->policy_no}",
            'metadata' => array_merge([
                'policy_no' => $policy->policy_no,
                'policy_holder' => $policy->customer->name,
                'insurance_company' => $policy->insuranceCompany->name ?? 'Unknown',
            ], $metadata),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'success' => true,
        ]);
    }

    public static function logFailure(string $action, string $reason, array $metadata = []): void
    {
        $customer = auth('customer')->user();

        if (! $customer) {
            return;
        }

        self::create([
            'customer_id' => $customer->id,
            'action' => $action,
            'description' => "Failed: {$reason}",
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'success' => false,
            'failure_reason' => $reason,
        ]);
    }
}
