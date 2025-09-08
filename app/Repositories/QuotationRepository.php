<?php

namespace App\Repositories;

use App\Contracts\Repositories\QuotationRepositoryInterface;
use App\Models\Quotation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class QuotationRepository implements QuotationRepositoryInterface
{
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

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
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

    public function findById(int $id): ?Quotation
    {
        return Quotation::with(['customer', 'quotationCompanies.insuranceCompany'])
                       ->find($id);
    }

    public function create(array $data): Quotation
    {
        return Quotation::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Quotation::whereId($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Quotation::whereId($id)->delete();
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