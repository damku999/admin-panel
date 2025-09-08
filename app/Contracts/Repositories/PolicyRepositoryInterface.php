<?php

namespace App\Contracts\Repositories;

use App\Models\CustomerInsurance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PolicyRepositoryInterface
{
    /**
     * Get all policies with optional filters.
     */
    public function getAll(array $filters = []): Collection;

    /**
     * Get paginated policies with search and filters.
     */
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Find policy by ID.
     */
    public function findById(int $id): ?CustomerInsurance;

    /**
     * Create a new policy.
     */
    public function create(array $data): CustomerInsurance;

    /**
     * Update policy by ID.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete policy by ID.
     */
    public function delete(int $id): bool;

    /**
     * Get policies by customer ID.
     */
    public function getByCustomer(int $customerId): Collection;

    /**
     * Get policies by insurance company.
     */
    public function getByInsuranceCompany(int $companyId): Collection;

    /**
     * Get active policies.
     */
    public function getActive(): Collection;

    /**
     * Get expired policies.
     */
    public function getExpired(): Collection;

    /**
     * Get policies due for renewal within specified days.
     */
    public function getDueForRenewal(int $daysAhead = 30): Collection;

    /**
     * Get policies by family group.
     */
    public function getByFamilyGroup(int $familyGroupId): Collection;

    /**
     * Get policies by policy type.
     */
    public function getByPolicyType(int $policyTypeId): Collection;

    /**
     * Search policies by policy number or customer name.
     */
    public function search(string $query): Collection;

    /**
     * Get policy statistics.
     */
    public function getStatistics(): array;

    /**
     * Check if policy exists.
     */
    public function exists(int $id): bool;

    /**
     * Get policy count by status.
     */
    public function getCountByStatus(): array;
}