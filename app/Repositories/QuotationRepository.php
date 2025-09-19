<?php

namespace App\Repositories;

use App\Contracts\Repositories\QuotationRepositoryInterface;
use App\Models\Quotation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Quotation Repository
 *
 * Extends base repository functionality for Quotation-specific operations.
 * Common CRUD operations are inherited from AbstractBaseRepository.
 */
class QuotationRepository extends AbstractBaseRepository implements QuotationRepositoryInterface
{
    protected string $modelClass = Quotation::class;
    protected array $searchableFields = ['vehicle_number', 'make_model_variant'];

    /**
     * Get all quotations with optional filters.
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Quotation::with(['customer', 'quotationCompanies.insuranceCompany']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        return $query->latest()->get();
    }

    /**
     * Override base getPaginated to support complex filtering with relationships
     */
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $filters = $request->all();
        $query = Quotation::with(['customer', 'quotationCompanies.insuranceCompany']);

        // Search filter
        if (!empty($filters['search'])) {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('customer', function ($customerQuery) use ($searchTerm) {
                    $customerQuery->where('name', 'LIKE', $searchTerm)
                                 ->orWhere('mobile_number', 'LIKE', $searchTerm);
                })
                ->orWhere('vehicle_number', 'LIKE', $searchTerm)
                ->orWhere('make_model_variant', 'LIKE', $searchTerm);
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Customer filter
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Override findById to include relationships
     */
    public function findById(int $id)
    {
        return Quotation::with(['customer', 'quotationCompanies.insuranceCompany'])
                       ->find($id);
    }

    /**
     * Override update method to match interface signature
     */
    public function update($entity, array $data)
    {
        if (is_int($entity)) {
            return Quotation::whereId($entity)->update($data);
        }
        return parent::update($entity, $data);
    }

    /**
     * Override delete method to match interface signature
     */
    public function delete($entity): bool
    {
        if (is_int($entity)) {
            return Quotation::whereId($entity)->delete();
        }
        return parent::delete($entity);
    }

    public function getByCustomer(int $customerId): Collection
    {
        return Quotation::with(['quotationCompanies.insuranceCompany'])
                       ->where('customer_id', $customerId)
                       ->latest()
                       ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return Quotation::with(['customer', 'quotationCompanies.insuranceCompany'])
                       ->where('status', $status)
                       ->get();
    }

    public function getRecent(int $limit = 10): Collection
    {
        return Quotation::with(['customer', 'quotationCompanies.insuranceCompany'])
                       ->latest()
                       ->limit($limit)
                       ->get();
    }

    public function search(string $query): Collection
    {
        $searchTerm = '%' . trim($query) . '%';
        return Quotation::with(['customer', 'quotationCompanies.insuranceCompany'])
                       ->where(function ($q) use ($searchTerm) {
                           $q->whereHas('customer', function ($customerQuery) use ($searchTerm) {
                               $customerQuery->where('name', 'LIKE', $searchTerm)
                                            ->orWhere('mobile_number', 'LIKE', $searchTerm);
                           })
                           ->orWhere('vehicle_number', 'LIKE', $searchTerm)
                           ->orWhere('make_model_variant', 'LIKE', $searchTerm);
                       })
                       ->latest()
                       ->get();
    }

    public function getSentQuotations(): Collection
    {
        return Quotation::with(['customer', 'quotationCompanies.insuranceCompany'])
                       ->where('status', 'Sent')
                       ->whereNotNull('sent_at')
                       ->latest('sent_at')
                       ->get();
    }

    public function getPendingQuotations(): Collection
    {
        return Quotation::with(['customer', 'quotationCompanies.insuranceCompany'])
                       ->where('status', 'Draft')
                       ->orWhere('status', 'Generated')
                       ->latest()
                       ->get();
    }

    public function getCountByStatus(): array
    {
        return Quotation::selectRaw('status, COUNT(*) as count')
                       ->groupBy('status')
                       ->pluck('count', 'status')
                       ->toArray();
    }

    public function exists(int $id): bool
    {
        return Quotation::where('id', $id)->exists();
    }
}