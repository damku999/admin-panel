<?php

namespace App\Repositories;

use App\Contracts\Repositories\PolicyTypeRepositoryInterface;
use App\Models\PolicyType;

/**
 * Policy Type Repository
 *
 * Handles PolicyType entity data access operations.
 * Inherits common CRUD operations from AbstractBaseRepository.
 */
class PolicyTypeRepository extends AbstractBaseRepository implements PolicyTypeRepositoryInterface
{
    /**
     * The model class name
     *
     * @var class-string<PolicyType>
     */
    protected string $modelClass = PolicyType::class;

    /**
     * Searchable fields for the getPaginated method
     * PolicyType-specific search includes name
     *
     * @var array<string>
     */
    protected array $searchableFields = ['name'];

    // All CRUD operations are now inherited from AbstractBaseRepository
    // Add policy type-specific methods here if needed in the future
}
