<?php

namespace App\Contracts\Repositories;

use App\Models\Claim;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Claim Repository Interface
 *
 * Defines methods for Claim data access operations.
 * Extends BaseRepositoryInterface for common CRUD operations.
 */
interface ClaimRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get paginated list of claims with advanced filtering and search
     *
     * @param Request $request
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getClaimsWithFilters(Request $request, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get claims by status
     *
     * @param bool $status
     * @return Collection
     */
    public function getClaimsByStatus(bool $status): Collection;

    /**
     * Get claims by insurance type
     *
     * @param string $insuranceType
     * @return Collection
     */
    public function getClaimsByInsuranceType(string $insuranceType): Collection;

    /**
     * Get claims within date range
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return Collection
     */
    public function getClaimsByDateRange(string $dateFrom, string $dateTo): Collection;

    /**
     * Get claim statistics for dashboard
     *
     * @return array
     */
    public function getClaimStatistics(): array;

    /**
     * Search claims by multiple criteria
     *
     * @param string $searchTerm
     * @param int $limit
     * @return Collection
     */
    public function searchClaims(string $searchTerm, int $limit = 20): Collection;

    /**
     * Get claims for specific customer
     *
     * @param int $customerId
     * @return Collection
     */
    public function getClaimsByCustomer(int $customerId): Collection;

    /**
     * Get claims for specific customer insurance
     *
     * @param int $customerInsuranceId
     * @return Collection
     */
    public function getClaimsByCustomerInsurance(int $customerInsuranceId): Collection;

    /**
     * Generate next claim number
     *
     * @return string
     */
    public function generateClaimNumber(): string;
}