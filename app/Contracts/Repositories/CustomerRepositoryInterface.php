<?php

namespace App\Contracts\Repositories;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CustomerRepositoryInterface
{
    /**
     * Get all customers with optional filters.
     */
    public function getAll(array $filters = []): Collection;

    /**
     * Get paginated customers with search and filters.
     */
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Find customer by ID.
     */
    public function findById(int $id): ?Customer;

    /**
     * Find customer by email.
     */
    public function findByEmail(string $email): ?Customer;

    /**
     * Find customer by mobile number.
     */
    public function findByMobileNumber(string $mobileNumber): ?Customer;

    /**
     * Create a new customer.
     */
    public function create(array $data): Customer;

    /**
     * Update customer by ID.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete customer by ID.
     */
    public function delete(int $id): bool;

    /**
     * Get active customers for selection.
     */
    public function getActive(): Collection;

    /**
     * Get customers by family group.
     */
    public function getByFamilyGroup(int $familyGroupId): Collection;

    /**
     * Get customers by type (Retail/Corporate).
     */
    public function getByType(string $type): Collection;

    /**
     * Search customers by name, email, or mobile.
     */
    public function search(string $query): Collection;

    /**
     * Update customer status.
     */
    public function updateStatus(int $id, int $status): bool;

    /**
     * Check if customer exists by ID.
     */
    public function exists(int $id): bool;

    /**
     * Get customer count.
     */
    public function count(): int;
}