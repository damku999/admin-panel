<?php

namespace App\Repositories;

use App\Contracts\Repositories\PolicyRepositoryInterface;
use App\Models\CustomerInsurance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class PolicyRepository implements PolicyRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        $query = CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType', 'premiumType']);

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['insurance_company_id'])) {
            $query->where('insurance_company_id', $filters['insurance_company_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->get();
    }

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType', 'premiumType']);

        // Search filter
        if (!empty($filters['search'])) {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('policy_number', 'LIKE', $searchTerm)
                  ->orWhereHas('customer', function ($customerQuery) use ($searchTerm) {
                      $customerQuery->where('name', 'LIKE', $searchTerm)
                                   ->orWhere('mobile_number', 'LIKE', $searchTerm);
                  });
            });
        }

        // Customer filter
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        // Insurance company filter
        if (!empty($filters['insurance_company_id'])) {
            $query->where('insurance_company_id', $filters['insurance_company_id']);
        }

        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Policy type filter
        if (!empty($filters['policy_type_id'])) {
            $query->where('policy_type_id', $filters['policy_type_id']);
        }

        // Date range filter
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById(int $id): ?CustomerInsurance
    {
        return CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType', 'premiumType'])
                                ->find($id);
    }

    public function create(array $data): CustomerInsurance
    {
        return CustomerInsurance::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return CustomerInsurance::whereId($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return CustomerInsurance::whereId($id)->delete();
    }

    public function getByCustomer(int $customerId): Collection
    {
        return CustomerInsurance::with(['insuranceCompany', 'policyType', 'premiumType'])
                                ->where('customer_id', $customerId)
                                ->latest()
                                ->get();
    }

    public function getByInsuranceCompany(int $companyId): Collection
    {
        return CustomerInsurance::with(['customer', 'policyType', 'premiumType'])
                                ->where('insurance_company_id', $companyId)
                                ->latest()
                                ->get();
    }

    public function getActive(): Collection
    {
        return CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
                                ->where('status', 1)
                                ->where('policy_end_date', '>', now())
                                ->get();
    }

    public function getExpired(): Collection
    {
        return CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
                                ->where('policy_end_date', '<=', now())
                                ->get();
    }

    public function getDueForRenewal(int $daysAhead = 30): Collection
    {
        $targetDate = Carbon::now()->addDays($daysAhead);
        
        return CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
                                ->where('status', 1)
                                ->where('policy_end_date', '>', now())
                                ->where('policy_end_date', '<=', $targetDate)
                                ->orderBy('policy_end_date')
                                ->get();
    }

    public function getByFamilyGroup(int $familyGroupId): Collection
    {
        return CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
                                ->whereHas('customer', function ($query) use ($familyGroupId) {
                                    $query->where('family_group_id', $familyGroupId);
                                })
                                ->latest()
                                ->get();
    }

    public function getByPolicyType(int $policyTypeId): Collection
    {
        return CustomerInsurance::with(['customer', 'insuranceCompany'])
                                ->where('policy_type_id', $policyTypeId)
                                ->latest()
                                ->get();
    }

    public function search(string $query): Collection
    {
        $searchTerm = '%' . trim($query) . '%';
        
        return CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
                                ->where(function ($q) use ($searchTerm) {
                                    $q->where('policy_number', 'LIKE', $searchTerm)
                                      ->orWhereHas('customer', function ($customerQuery) use ($searchTerm) {
                                          $customerQuery->where('name', 'LIKE', $searchTerm)
                                                       ->orWhere('mobile_number', 'LIKE', $searchTerm);
                                      });
                                })
                                ->latest()
                                ->get();
    }

    public function getStatistics(): array
    {
        $totalPolicies = CustomerInsurance::count();
        $activePolicies = CustomerInsurance::where('status', 1)
                                          ->where('policy_end_date', '>', now())
                                          ->count();
        $expiredPolicies = CustomerInsurance::where('policy_end_date', '<=', now())
                                           ->count();
        $renewalsDue = CustomerInsurance::where('status', 1)
                                       ->where('policy_end_date', '>', now())
                                       ->where('policy_end_date', '<=', Carbon::now()->addDays(30))
                                       ->count();

        return [
            'total' => $totalPolicies,
            'active' => $activePolicies,
            'expired' => $expiredPolicies,
            'renewals_due' => $renewalsDue,
        ];
    }

    public function exists(int $id): bool
    {
        return CustomerInsurance::where('id', $id)->exists();
    }

    public function getCountByStatus(): array
    {
        return CustomerInsurance::selectRaw('status, COUNT(*) as count')
                                ->groupBy('status')
                                ->pluck('count', 'status')
                                ->toArray();
    }
}