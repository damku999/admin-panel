<?php

namespace App\Contracts\Services;

use App\Models\PremiumType;

interface PremiumTypeServiceInterface
{
    /**
     * Create a new premium type
     *
     * @param array $data
     * @return PremiumType
     * @throws \Throwable
     */
    public function createPremiumType(array $data): PremiumType;

    /**
     * Update an existing premium type
     *
     * @param PremiumType $premiumType
     * @param array $data
     * @return bool
     * @throws \Throwable
     */
    public function updatePremiumType(PremiumType $premiumType, array $data): bool;

    /**
     * Delete a premium type
     *
     * @param PremiumType $premiumType
     * @return bool
     * @throws \Throwable
     */
    public function deletePremiumType(PremiumType $premiumType): bool;

    /**
     * Update premium type status
     *
     * @param int $premiumTypeId
     * @param int $status
     * @return bool
     * @throws \Throwable
     */
    public function updateStatus(int $premiumTypeId, int $status): bool;
}