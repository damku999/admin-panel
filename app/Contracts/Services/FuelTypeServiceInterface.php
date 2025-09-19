<?php

namespace App\Contracts\Services;

use App\Models\FuelType;

interface FuelTypeServiceInterface
{
    /**
     * Create a new fuel type
     *
     * @param array $data
     * @return FuelType
     * @throws \Throwable
     */
    public function createFuelType(array $data): FuelType;

    /**
     * Update an existing fuel type
     *
     * @param FuelType $fuelType
     * @param array $data
     * @return bool
     * @throws \Throwable
     */
    public function updateFuelType(FuelType $fuelType, array $data): bool;

    /**
     * Delete a fuel type
     *
     * @param FuelType $fuelType
     * @return bool
     * @throws \Throwable
     */
    public function deleteFuelType(FuelType $fuelType): bool;

    /**
     * Update fuel type status
     *
     * @param int $fuelTypeId
     * @param int $status
     * @return bool
     * @throws \Throwable
     */
    public function updateStatus(int $fuelTypeId, int $status): bool;
}