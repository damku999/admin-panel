<?php

namespace App\Contracts\Services;

use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Claim Service Interface
 *
 * Defines business logic operations for Claim management.
 * Includes document management, stage tracking, and policy search functionality.
 */
interface ClaimServiceInterface
{
    /**
     * Get paginated list of claims with filters and search
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getClaims(Request $request): LengthAwarePaginator;

    /**
     * Create a new claim with default documents and initial stage
     *
     * @param StoreClaimRequest $request
     * @return Claim
     */
    public function createClaim(StoreClaimRequest $request): Claim;

    /**
     * Update an existing claim
     *
     * @param UpdateClaimRequest $request
     * @param Claim $claim
     * @return bool
     */
    public function updateClaim(UpdateClaimRequest $request, Claim $claim): bool;

    /**
     * Update claim status
     *
     * @param int $claimId
     * @param bool $status
     * @return bool
     */
    public function updateClaimStatus(int $claimId, bool $status): bool;

    /**
     * Delete a claim (soft delete)
     *
     * @param Claim $claim
     * @return bool
     */
    public function deleteClaim(Claim $claim): bool;

    /**
     * Search for policies/insurances with wildcard functionality
     *
     * @param string $searchTerm
     * @return array
     */
    public function searchPolicies(string $searchTerm): array;

    /**
     * Get claim statistics for dashboard
     *
     * @return array
     */
    public function getClaimStatistics(): array;
}