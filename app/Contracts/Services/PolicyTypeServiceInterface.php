<?php

namespace App\Contracts\Services;

use App\Models\PolicyType;

interface PolicyTypeServiceInterface
{
    /**
     * Create a new policy type
     *
     * @param array $data
     * @return PolicyType
     * @throws \Throwable
     */
    public function createPolicyType(array $data): PolicyType;

    /**
     * Update an existing policy type
     *
     * @param PolicyType $policyType
     * @param array $data
     * @return bool
     * @throws \Throwable
     */
    public function updatePolicyType(PolicyType $policyType, array $data): bool;

    /**
     * Delete a policy type
     *
     * @param PolicyType $policyType
     * @return bool
     * @throws \Throwable
     */
    public function deletePolicyType(PolicyType $policyType): bool;

    /**
     * Update policy type status
     *
     * @param int $policyTypeId
     * @param int $status
     * @return bool
     * @throws \Throwable
     */
    public function updateStatus(int $policyTypeId, int $status): bool;
}