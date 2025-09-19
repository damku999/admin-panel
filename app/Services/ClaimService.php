<?php

namespace App\Services;

use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\Claim;
use App\Models\CustomerInsurance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

/**
 * Claim Service
 *
 * Handles Claim business logic including document and stage management.
 * Inherits transaction management from BaseService.
 */
class ClaimService extends BaseService
{
    /**
     * Get paginated list of claims with filters and search.
     */
    public function getClaims(Request $request): LengthAwarePaginator
    {
        $query = Claim::with([
            'customer:id,name,email,mobile_number',
            'customerInsurance:id,policy_no,registration_no,insurance_company_id',
            'customerInsurance.insuranceCompany:id,name',
            'currentStage:id,claim_id,stage_name'
        ]);

        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function (Builder $q) use ($search) {
                $q->where('claim_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('customer', function (Builder $customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%")
                                   ->orWhere('mobile_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customerInsurance', function (Builder $insuranceQuery) use ($search) {
                      $insuranceQuery->where('policy_no', 'like', "%{$search}%")
                                    ->orWhere('registration_no', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by insurance type
        if ($request->filled('insurance_type')) {
            $query->where('insurance_type', $request->input('insurance_type'));
        }

        // Filter by status
        if ($request->filled('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('incident_date', '>=', formatDateForDatabase($request->input('date_from')));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('incident_date', '<=', formatDateForDatabase($request->input('date_to')));
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Validate sort field to prevent SQL injection
        $allowedSortFields = [
            'claim_number', 'insurance_type', 'incident_date', 'status', 'created_at', 'updated_at'
        ];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get paginated results
        $perPage = $request->input('per_page', 15);
        return $query->paginate($perPage);
    }

    /**
     * Create a new claim.
     */
    public function createClaim(StoreClaimRequest $request): Claim
    {
        return $this->createInTransaction(function () use ($request) {
            // Get customer insurance details
            $customerInsurance = CustomerInsurance::with('customer')->findOrFail($request->customer_insurance_id);

            // Generate claim number
            $claimNumber = Claim::generateClaimNumber();

            // Create claim
            $claimData = $request->validated();
            $claimData['claim_number'] = $claimNumber;
            $claimData['customer_id'] = $customerInsurance->customer_id;

            // Set WhatsApp number from customer if not provided
            if (empty($claimData['whatsapp_number'])) {
                $claimData['whatsapp_number'] = $customerInsurance->customer->mobile_number;
            }

            $claim = Claim::create($claimData);

            // Create default documents based on insurance type
            $claim->createDefaultDocuments();

            // Create initial stage
            $claim->createInitialStage();

            // Create basic liability detail record with correct claim type
            $claim->liabilityDetail()->create([
                'claim_type' => $claim->insurance_type === 'Health' ? 'Cashless' : 'Reimbursement',
                'notes' => 'Initial liability detail record created',
            ]);

            // Send email notification if enabled (after transaction)
            $claim->sendClaimCreatedNotification();

            Log::info('Claim created successfully', [
                'claim_id' => $claim->id,
                'claim_number' => $claim->claim_number,
                'customer_id' => $claim->customer_id,
                'user_id' => auth()->id(),
            ]);

            return $claim;
        });
    }

    /**
     * Update an existing claim.
     */
    public function updateClaim(UpdateClaimRequest $request, Claim $claim): bool
    {
        return $this->updateInTransaction(function () use ($request, $claim) {
            // Get customer insurance details if changed
            if ($request->customer_insurance_id !== $claim->customer_insurance_id) {
                $customerInsurance = CustomerInsurance::with('customer')->findOrFail($request->customer_insurance_id);
                $updateData = $request->validated();
                $updateData['customer_id'] = $customerInsurance->customer_id;
            } else {
                $updateData = $request->validated();
            }

            // Set WhatsApp number from customer if not provided
            if (empty($updateData['whatsapp_number']) && isset($customerInsurance)) {
                $updateData['whatsapp_number'] = $customerInsurance->customer->mobile_number;
            }

            $updated = $claim->update($updateData);

            // Sync insurance_type to liability detail's claim_type if insurance_type was updated
            if (isset($updateData['insurance_type']) && $claim->liabilityDetail) {
                $newClaimType = $updateData['insurance_type'] === 'Health' ? 'Cashless' : 'Reimbursement';
                $claim->liabilityDetail->update(['claim_type' => $newClaimType]);
            }

            if ($updated) {
                Log::info('Claim updated successfully', [
                    'claim_id' => $claim->id,
                    'claim_number' => $claim->claim_number,
                    'user_id' => auth()->id(),
                ]);
            }

            return $updated;
        });
    }

    /**
     * Update claim status.
     */
    public function updateClaimStatus(int $claimId, bool $status): bool
    {
        try {
            $claim = Claim::findOrFail($claimId);
            $updated = $claim->update(['status' => $status]);

            if ($updated) {
                Log::info('Claim status updated', [
                    'claim_id' => $claimId,
                    'status' => $status,
                    'user_id' => auth()->id(),
                ]);
            }

            return $updated;

        } catch (\Exception $e) {
            Log::error('Failed to update claim status', [
                'claim_id' => $claimId,
                'status' => $status,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a claim (soft delete).
     */
    public function deleteClaim(Claim $claim): bool
    {
        try {
            $deleted = $claim->delete();

            if ($deleted) {
                Log::info('Claim deleted successfully', [
                    'claim_id' => $claim->id,
                    'claim_number' => $claim->claim_number,
                    'user_id' => auth()->id(),
                ]);
            }

            return $deleted;

        } catch (\Exception $e) {
            Log::error('Failed to delete claim', [
                'claim_id' => $claim->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }

    /**
     * Search for policies/insurances with wildcard functionality.
     * This method provides the wildcard search for policy selection in the create form.
     */
    public function searchPolicies(string $searchTerm): array
    {
        if (strlen($searchTerm) < 3) {
            return [];
        }

        $policies = CustomerInsurance::with([
            'customer:id,name,email,mobile_number',
            'insuranceCompany:id,name',
            'policyType:id,name'
        ])
        ->where(function (Builder $query) use ($searchTerm) {
            $query->where('policy_no', 'like', "%{$searchTerm}%")
                  ->orWhere('registration_no', 'like', "%{$searchTerm}%")
                  ->orWhereHas('customer', function (Builder $customerQuery) use ($searchTerm) {
                      $customerQuery->where('name', 'like', "%{$searchTerm}%")
                                   ->orWhere('email', 'like', "%{$searchTerm}%")
                                   ->orWhere('mobile_number', 'like', "%{$searchTerm}%");
                  });
        })
        ->where('status', true) // Only active policies
        ->limit(20)
        ->get();

        return $policies->map(function ($policy) {
            return [
                'id' => $policy->id,
                'text' => $this->formatPolicyText($policy),
                'customer_name' => $policy->customer->name ?? '',
                'customer_email' => $policy->customer->email ?? '',
                'customer_mobile' => $policy->customer->mobile_number ?? '',
                'policy_no' => $policy->policy_no ?? '',
                'registration_no' => $policy->registration_no ?? '',
                'insurance_company' => $policy->insuranceCompany->name ?? '',
                'policy_type' => $policy->policyType->name ?? '',
                'suggested_insurance_type' => $this->suggestInsuranceType($policy),
            ];
        })->toArray();
    }

    /**
     * Format policy text for display in dropdown.
     */
    private function formatPolicyText(CustomerInsurance $policy): string
    {
        $parts = [];

        if ($policy->customer) {
            $parts[] = $policy->customer->name;
        }

        if ($policy->policy_no) {
            $parts[] = "Policy: {$policy->policy_no}";
        }

        if ($policy->registration_no) {
            $parts[] = "Reg: {$policy->registration_no}";
        }

        if ($policy->insuranceCompany) {
            $parts[] = $policy->insuranceCompany->name;
        }

        return implode(' - ', $parts);
    }

    /**
     * Suggest insurance type based on policy type.
     */
    private function suggestInsuranceType(CustomerInsurance $policy): string
    {
        // Check policy type or other indicators to suggest insurance type
        $policyTypeName = strtolower($policy->policyType->name ?? '');

        // Vehicle insurance indicators
        $vehicleKeywords = ['motor', 'vehicle', 'car', 'bike', 'auto', 'comprehensive', 'third party'];
        foreach ($vehicleKeywords as $keyword) {
            if (strpos($policyTypeName, $keyword) !== false) {
                return 'Vehicle';
            }
        }

        // Health insurance indicators
        $healthKeywords = ['health', 'medical', 'mediclaim', 'hospital', 'disease'];
        foreach ($healthKeywords as $keyword) {
            if (strpos($policyTypeName, $keyword) !== false) {
                return 'Health';
            }
        }

        // If registration number exists, likely vehicle
        if (!empty($policy->registration_no)) {
            return 'Vehicle';
        }

        // Default to Health if unclear
        return 'Health';
    }

    /**
     * Get claim statistics for dashboard.
     */
    public function getClaimStatistics(): array
    {
        try {
            $stats = [
                'total_claims' => Claim::count(),
                'active_claims' => Claim::where('status', true)->count(),
                'inactive_claims' => Claim::where('status', false)->count(),
                'health_claims' => Claim::where('insurance_type', 'Health')->count(),
                'vehicle_claims' => Claim::where('insurance_type', 'Vehicle')->count(),
                'this_month_claims' => Claim::whereMonth('created_at', now()->month)
                                          ->whereYear('created_at', now()->year)
                                          ->count(),
                'this_week_claims' => Claim::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
            ];

            return $stats;

        } catch (\Exception $e) {
            Log::error('Failed to get claim statistics', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return [];
        }
    }
}