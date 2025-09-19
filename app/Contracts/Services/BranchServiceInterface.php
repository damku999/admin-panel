<?php

namespace App\Contracts\Services;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Branch Service Interface
 *
 * Defines business logic operations for Branch management.
 * Handles branch operations, status management, and reporting.
 */
interface BranchServiceInterface
{
    /**
     * Get paginated list of branches with filters
     *
     * @param Request $request
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getBranches(Request $request, int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new branch
     *
     * @param array $data
     * @return Branch
     */
    public function createBranch(array $data): Branch;

    /**
     * Update an existing branch
     *
     * @param Branch $branch
     * @param array $data
     * @return bool
     */
    public function updateBranch(Branch $branch, array $data): bool;

    /**
     * Delete a branch
     *
     * @param Branch $branch
     * @return bool
     */
    public function deleteBranch(Branch $branch): bool;

    /**
     * Update branch status
     *
     * @param int $branchId
     * @param bool $status
     * @return bool
     */
    public function updateBranchStatus(int $branchId, bool $status): bool;

    /**
     * Get active branches for dropdown/select options
     *
     * @return Collection
     */
    public function getActiveBranches(): Collection;

    /**
     * Get branch with customer insurances count
     *
     * @param int $branchId
     * @return Branch|null
     */
    public function getBranchWithInsurancesCount(int $branchId): ?Branch;

    /**
     * Search branches by name
     *
     * @param string $searchTerm
     * @param int $limit
     * @return Collection
     */
    public function searchBranches(string $searchTerm, int $limit = 20): Collection;

    /**
     * Get branch statistics
     *
     * @return array
     */
    public function getBranchStatistics(): array;

    /**
     * Get all branches for export
     *
     * @return Collection
     */
    public function getAllBranchesForExport(): Collection;
}