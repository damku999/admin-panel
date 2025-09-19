<?php

namespace App\Repositories;

use App\Contracts\Repositories\PremiumTypeRepositoryInterface;
use App\Models\PremiumType;

/**
 * Premium Type Repository
 *
 * Handles PremiumType entity data access operations.
 * Inherits common CRUD operations from AbstractBaseRepository.
 */
class PremiumTypeRepository extends AbstractBaseRepository implements PremiumTypeRepositoryInterface
{
    /**
     * The model class name
     *
     * @var class-string<PremiumType>
     */
    protected string $modelClass = PremiumType::class;

    /**
     * Searchable fields for the getPaginated method
     * PremiumType-specific search includes name
     *
     * @var array<string>
     */
    protected array $searchableFields = ['name'];

    // All CRUD operations are now inherited from AbstractBaseRepository
    // Add premium type-specific methods here if needed in the future
}
