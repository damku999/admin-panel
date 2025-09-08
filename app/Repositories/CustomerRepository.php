<?php

namespace App\Repositories;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        $query = Customer::query();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
        }

        return $query->get();
    }

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Customer::query();

        // Search filter
        if (!empty($filters['search'])) {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('email', 'LIKE', $searchTerm)
                  ->orWhere('mobile_number', 'LIKE', $searchTerm);
            });
        }

        // Type filter
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Date range filter
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
        }

        // Sorting
        $sortField = $filters['sort_field'] ?? 'name';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    public function findByMobileNumber(string $mobileNumber): ?Customer
    {
        return Customer::where('mobile_number', $mobileNumber)->first();
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Customer::whereId($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Customer::whereId($id)->delete();
    }

    public function getActive(): Collection
    {
        return Customer::where('status', 1)->orderBy('name')->get();
    }

    public function getByFamilyGroup(int $familyGroupId): Collection
    {
        return Customer::where('family_group_id', $familyGroupId)->get();
    }

    public function getByType(string $type): Collection
    {
        return Customer::where('type', $type)->get();
    }

    public function search(string $query): Collection
    {
        $searchTerm = '%' . trim($query) . '%';
        return Customer::where('name', 'LIKE', $searchTerm)
                      ->orWhere('email', 'LIKE', $searchTerm)
                      ->orWhere('mobile_number', 'LIKE', $searchTerm)
                      ->get();
    }

    public function updateStatus(int $id, int $status): bool
    {
        return Customer::whereId($id)->update(['status' => $status]);
    }

    public function exists(int $id): bool
    {
        return Customer::where('id', $id)->exists();
    }

    public function count(): int
    {
        return Customer::count();
    }
}