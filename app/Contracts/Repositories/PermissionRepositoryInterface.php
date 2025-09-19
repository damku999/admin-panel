<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;

/**
 * Permission Repository Interface
 *
 * Defines methods for Permission data access operations using Spatie Permission.
 * Extends BaseRepositoryInterface for common CRUD operations.
 */
interface PermissionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get paginated list of permissions with filtering and search
     *
     * @param Request $request
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPermissionsWithFilters(Request $request, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get permission with roles loaded
     *
     * @param int $permissionId
     * @return Permission|null
     */
    public function getPermissionWithRoles(int $permissionId): ?Permission;

    /**
     * Get permissions with roles count
     *
     * @return Collection
     */
    public function getPermissionsWithRolesCount(): Collection;

    /**
     * Search permissions by name
     *
     * @param string $searchTerm
     * @param int $limit
     * @return Collection
     */
    public function searchPermissions(string $searchTerm, int $limit = 20): Collection;

    /**
     * Get permissions for specific guard
     *
     * @param string $guardName
     * @return Collection
     */
    public function getPermissionsByGuard(string $guardName = 'web'): Collection;

    /**
     * Get permissions by module/prefix
     *
     * @param string $module
     * @return Collection
     */
    public function getPermissionsByModule(string $module): Collection;

    /**
     * Get permission statistics
     *
     * @return array
     */
    public function getPermissionStatistics(): array;

    /**
     * Get permissions assigned to specific role
     *
     * @param int $roleId
     * @return Collection
     */
    public function getPermissionsByRole(int $roleId): Collection;

    /**
     * Get permissions not assigned to specific role
     *
     * @param int $roleId
     * @param string $guardName
     * @return Collection
     */
    public function getUnassignedPermissions(int $roleId, string $guardName = 'web'): Collection;
}