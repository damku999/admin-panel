<?php

namespace App\Repositories;

use App\Contracts\Repositories\FuelTypeRepositoryInterface;
use App\Models\FuelType;

/**
 * Fuel Type Repository
 *
 * Handles FuelType entity data access operations.
 * Inherits common CRUD operations from AbstractBaseRepository.
 */
class FuelTypeRepository extends AbstractBaseRepository implements FuelTypeRepositoryInterface
{
    /**
     * The model class name
     *
     * @var class-string<FuelType>
     */
    protected string $modelClass = FuelType::class;

    /**
     * Searchable fields for the getPaginated method
     * FuelType-specific search includes name
     *
     * @var array<string>
     */
    protected array $searchableFields = ['name'];

    // All CRUD operations are now inherited from AbstractBaseRepository
    // Add fuel type-specific methods here if needed in the future
}
