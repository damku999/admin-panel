<?php

namespace App\Repositories;

use App\Contracts\Repositories\ClaimRepositoryInterface;
use App\Models\Claim;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Claim Repository
 *
 * Handles Claim data access operations.
 * Inherits common CRUD operations from AbstractBaseRepository.
 */
class ClaimRepository extends AbstractBaseRepository implements ClaimRepositoryInterface
{
    /**
     * The model class name
     *
     * @var string
     */
    protected string $modelClass = Claim::class;

    /**
     * Searchable fields for the getPaginated method
     *
     * @var array
     */
    protected array $searchableFields = [
        'claim_number',
        'description',
        'incident_location',
        'whatsapp_number'
    ];

    /**
     * Get paginated list of claims with advanced filtering and search
     */
    public function getClaimsWithFilters(Request $request, int $perPage = 15): LengthAwarePaginator
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

        return $query->paginate($perPage);
    }

    /**
     * Get claims by status
     */
    public function getClaimsByStatus(bool $status): Collection
    {
        return Claim::where('status', $status)
            ->with(['customer', 'customerInsurance.insuranceCompany'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get claims by insurance type
     */
    public function getClaimsByInsuranceType(string $insuranceType): Collection
    {
        return Claim::where('insurance_type', $insuranceType)
            ->with(['customer', 'customerInsurance.insuranceCompany'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get claims within date range
     */
    public function getClaimsByDateRange(string $dateFrom, string $dateTo): Collection
    {
        return Claim::whereBetween('incident_date', [$dateFrom, $dateTo])
            ->with(['customer', 'customerInsurance.insuranceCompany'])
            ->orderBy('incident_date', 'desc')
            ->get();
    }

    /**
     * Get claim statistics for dashboard
     */
    public function getClaimStatistics(): array
    {
        return [
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
    }

    /**
     * Search claims by multiple criteria
     */
    public function searchClaims(string $searchTerm, int $limit = 20): Collection
    {
        if (strlen($searchTerm) < 2) {
            return collect();
        }

        return Claim::with(['customer', 'customerInsurance.insuranceCompany'])
            ->where(function (Builder $query) use ($searchTerm) {
                $query->where('claim_number', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhereHas('customer', function (Builder $customerQuery) use ($searchTerm) {
                          $customerQuery->where('name', 'like', "%{$searchTerm}%")
                                       ->orWhere('email', 'like', "%{$searchTerm}%")
                                       ->orWhere('mobile_number', 'like', "%{$searchTerm}%");
                      });
            })
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get claims for specific customer
     */
    public function getClaimsByCustomer(int $customerId): Collection
    {
        return Claim::where('customer_id', $customerId)
            ->with(['customerInsurance.insuranceCompany', 'currentStage'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get claims for specific customer insurance
     */
    public function getClaimsByCustomerInsurance(int $customerInsuranceId): Collection
    {
        return Claim::where('customer_insurance_id', $customerInsuranceId)
            ->with(['customer', 'customerInsurance.insuranceCompany', 'currentStage'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Generate next claim number
     */
    public function generateClaimNumber(): string
    {
        return Claim::generateClaimNumber();
    }
}