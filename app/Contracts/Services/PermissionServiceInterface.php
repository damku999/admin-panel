<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;

/**
 * Permission Service Interface
 *
 * Defines business logic operations for Permission management.
 * Handles permission operations, role assignments, and access control.
 */
interface PermissionServiceInterface
{
    /**
     * Get paginated list of permissions with filters
     *
     * @param Request $request
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPermissions(Request $request, int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new permission
     *
     * @param array $data
     * @return Permission
     */
    public function createPermission(array $data): Permission;

    /**
     * Update an existing permission
     *
     * @param Permission $permission
     * @param array $data
     * @return bool
     */
    public function updatePermission(Permission $permission, array $data): bool;

    /**
     * Delete a permission
     *
     * @param Permission $permission
     * @return bool
     */
    public function deletePermission(Permission $permission): bool;

    /**
     * Get all permissions for role assignment
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection;

    /**
     * Get permissions by role
     *
     * @param int $roleId
     * @return Collection
     */
    public function getPermissionsByRole(int $roleId): Collection;

    /**
     * Search permissions by name
     *
     * @param string $searchTerm
     * @param int $limit
     * @return Collection
     */
    public function searchPermissions(string $searchTerm, int $limit = 20): Collection;

    /**
     * Get permission statistics
     *
     * @return array
     */
    public function getPermissionStatistics(): array;

    /**
     * Sync permissions for a role
     *
     * @param int $roleId
     * @param array $permissionIds
     * @return bool
     */
    public function syncRolePermissions(int $roleId, array $permissionIds): bool;

    /**
     * Get all permissions grouped by module
     *
     * @return array
     */
    public function getPermissionsGroupedByModule(): array;
}