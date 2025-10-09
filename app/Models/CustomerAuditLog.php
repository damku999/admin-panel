<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
