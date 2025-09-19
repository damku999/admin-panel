<?php

namespace App\Repositories;

use App\Contracts\Repositories\CustomerInsuranceRepositoryInterface;
use App\Models\CustomerInsurance;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * Customer Insurance Repository
 *
 * Extends base repository functionality for CustomerInsurance-specific operations.
 * Common CRUD operations are inherited from AbstractBaseRepository.
 */
class CustomerInsuranceRepository extends AbstractBaseRepository implements CustomerInsuranceRepositoryInterface
{
    protected string $modelClass = CustomerInsurance::class;
    protected array $searchableFields = ['policy_no', 'registration_no'];

    /**
     * Override base getPaginated to support complex joins and filtering
     */
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = CustomerInsurance::select([
            'customer_insurances.*',
            'customers.name as customer_name',
            'branches.name as branch_name',
            'brokers.name as broker_name',
            'relationship_managers.name as relationship_manager_name',
            'premium_types.name AS policy_type_name'
        ])
        ->leftJoin('customers', 'customers.id', '=', 'customer_insurances.customer_id')
        ->leftJoin('branches', 'branches.id', '=', 'customer_insurances.branch_id')
        ->leftJoin('brokers', 'brokers.id', '=', 'customer_insurances.broker_id')
        ->leftJoin('relationship_managers', 'relationship_managers.id', '=', 'customer_insurances.relationship_manager_id')
        ->leftJoin('premium_types', 'premium_types.id', '=', 'customer_insurances.premium_type_id');

        // Apply filters
        if (!empty($request->search)) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('customers.name', 'LIKE', '%' . $search . '%')
                  ->orWhere('customer_insurances.policy_no', 'LIKE', '%' . $search . '%')
                  ->orWhere('customer_insurances.registration_no', 'LIKE', '%' . $search . '%');
            });
        }

        if (!empty($request->customer_id)) {
            $query->where('customer_insurances.customer_id', $request->customer_id);
        }

        if (!empty($request->insurance_company_id)) {
            $query->where('customer_insurances.insurance_company_id', $request->insurance_company_id);
        }

        if (!empty($request->status)) {
            $query->where('customer_insurances.status', $request->status);
        }

        return $query->orderBy('customer_insurances.created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find customer insurance with specific relations.
     */
    public function findWithRelations(int $id, array $relations = []): ?CustomerInsurance
    {
        $defaultRelations = ['customer', 'insuranceCompany', 'branch', 'broker', 'relationshipManager'];
        $loadRelations = empty($relations) ? $defaultRelations : $relations;

        return CustomerInsurance::with($loadRelations)->find($id);
    }

    /**
     * Get customer insurances by customer ID.
     */
    public function getByCustomerId(int $customerId): Collection
    {
        return CustomerInsurance::where('customer_id', $customerId)
            ->with(['insuranceCompany', 'branch', 'broker'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Override getAllForExport to include relationships.
     */
    public function getAllForExport(): Collection
    {
        return CustomerInsurance::with(['customer', 'insuranceCompany', 'branch', 'broker', 'relationshipManager'])->get();
    }

    /**
     * Get expiring policies within specified days.
     */
    public function getExpiringPolicies(int $days = 30): Collection
    {
        $expiryDate = Carbon::now()->addDays($days);

        return CustomerInsurance::with(['customer', 'insuranceCompany'])
            ->where('status', 1)
            ->where('expired_date', '<=', $expiryDate)
            ->where('expired_date', '>', Carbon::now())
            ->orderBy('expired_date', 'asc')
            ->get();
    }

    /**
     * Get active customer insurances.
     */
    public function getActiveCustomerInsurances(): Collection
    {
        return CustomerInsurance::where('status', true)
            ->orderBy('id', 'desc')
            ->get();
    }
}