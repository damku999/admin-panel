<?php

namespace App\Repositories;

use App\Contracts\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Abstract Base Repository
 *
 * Provides common CRUD implementation for all repositories.
 * Eliminates duplicate code across entity-specific repositories.
 *
 * @template T of Model
 */
abstract class AbstractBaseRepository implements BaseRepositoryInterface
{
    /**
     * The model class name
     *
     * @var class-string<T>
     */
    protected string $modelClass;

    /**
     * Searchable fields for the getPaginated method
     *
     * @var array<string>
     */
    protected array $searchableFields = ['name'];

    /**
     * Get paginated results with optional search and filtering
     *
     * @param Request $request
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->modelClass::select('*');

        if (!empty($request->search)) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                foreach ($this->searchableFields as $field) {
                    $q->orWhere($field, 'LIKE', '%' . $search . '%');
                }
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new entity
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->modelClass::create($data);
    }

    /**
     * Update an existing entity
     *
     * @param Model $entity
     * @param array $data
     * @return Model
     */
    public function update(Model $entity, array $data): Model
    {
        $entity->update($data);
        return $entity->fresh();
    }

    /**
     * Delete an entity
     *
     * @param Model $entity
     * @return bool
     */
    public function delete(Model $entity): bool
    {
        return $entity->delete();
    }

    /**
     * Find entity by ID
     *
     * @param int $id
     * @return Model|null
     */
    public function findById(int $id): ?Model
    {
        return $this->modelClass::find($id);
    }

    /**
     * Update entity status
     *
     * @param int $id
     * @param int $status
     * @return bool
     */
    public function updateStatus(int $id, int $status): bool
    {
        return $this->modelClass::where('id', $id)->update(['status' => $status]) > 0;
    }

    /**
     * Get all active entities
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->modelClass::where('status', 1)->get();
    }

    /**
     * Get all entities for export (no pagination)
     *
     * @return Collection
     */
    public function getAllForExport(): Collection
    {
        return $this->modelClass::all();
    }
}