<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Base Repository Interface
 *
 * Provides common CRUD operations for all repository implementations.
 * This interface eliminates code duplication across entity-specific repositories.
 *
 * @template T of Model
 */
interface BaseRepositoryInterface
{
    /**
     * Get paginated results with optional search and filtering
     *
     * @param Request $request
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new entity
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update an existing entity
     *
     * @param Model $entity
     * @param array $data
     * @return Model
     */
    public function update(Model $entity, array $data): Model;

    /**
     * Delete an entity
     *
     * @param Model $entity
     * @return bool
     */
    public function delete(Model $entity): bool;

    /**
     * Find entity by ID
     *
     * @param int $id
     * @return Model|null
     */
    public function findById(int $id): ?Model;

    /**
     * Update entity status
     *
     * @param int $id
     * @param int $status
     * @return bool
     */
    public function updateStatus(int $id, int $status): bool;

    /**
     * Get all active entities
     *
     * @return Collection
     */
    public function getActive(): Collection;

    /**
     * Get all entities for export (no pagination)
     *
     * @return Collection
     */
    public function getAllForExport(): Collection;
}