<?php

namespace App\Contracts\Repositories;

use App\Models\FamilyGroup;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Family Group Repository Interface
 *
 * Defines methods for FamilyGroup data access operations.
 * Extends BaseRepositoryInterface for common CRUD operations.
 */
interface FamilyGroupRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get paginated list of family groups with filtering and search
     *
     * @param Request $request
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getFamilyGroupsWithFilters(Request $request, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get family group with all relationships loaded
     *
     * @param int $familyGroupId
     * @return FamilyGroup|null
     */
    public function getFamilyGroupWithMembers(int $familyGroupId): ?FamilyGroup;

    /**
     * Get family groups by family head
     *
     * @param int $familyHeadId
     * @return Collection
     */
    public function getFamilyGroupsByHead(int $familyHeadId): Collection;

    /**
     * Get family groups by customer (either as head or member)
     *
     * @param int $customerId
     * @return Collection
     */
    public function getFamilyGroupsByCustomer(int $customerId): Collection;

    /**
     * Get available customers for family group creation
     * (customers not in any family group)
     *
     * @return Collection
     */
    public function getAvailableCustomers(): Collection;

    /**
     * Get available customers for family group editing
     * (customers not in other family groups)
     *
     * @param int $familyGroupId
     * @return Collection
     */
    public function getAvailableCustomersForEdit(int $familyGroupId): Collection;

    /**
     * Check if customer is already in a family group
     *
     * @param int $customerId
     * @return bool
     */
    public function isCustomerInFamilyGroup(int $customerId): bool;

    /**
     * Get family members for a specific family group
     *
     * @param int $familyGroupId
     * @return Collection
     */
    public function getFamilyMembers(int $familyGroupId): Collection;

    /**
     * Update family head for a family group
     *
     * @param int $familyGroupId
     * @param int $newFamilyHeadId
     * @return bool
     */
    public function updateFamilyHead(int $familyGroupId, int $newFamilyHeadId): bool;

    /**
     * Remove customer from family group
     *
     * @param int $customerId
     * @return bool
     */
    public function removeCustomerFromFamilyGroup(int $customerId): bool;

    /**
     * Get family group statistics
     *
     * @return array
     */
    public function getFamilyGroupStatistics(): array;

    /**
     * Search family groups by name or family head
     *
     * @param string $searchTerm
     * @param int $limit
     * @return Collection
     */
    public function searchFamilyGroups(string $searchTerm, int $limit = 20): Collection;

    /**
     * Get all family groups with relationships for export
     *
     * @return Collection
     */
    public function getAllFamilyGroupsWithRelationships(): Collection;
}