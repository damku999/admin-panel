<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

/**
 * Role Service Interface
 *
 * Defines business logic operations for Role management.
 * Handles role operations, permission assignments, and user role management.
 */
interface RoleServiceInterface
{
    /**
     * Get paginated list of roles with filters
     *
     * @param Request $request
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getRoles(Request $request, int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new role
     *
     * @param array $data
     * @return Role
     */
    public function createRole(array $data): Role;

    /**
     * Update an existing role
     *
     * @param Role $role
     * @param array $data
     * @return bool
     */
    public function updateRole(Role $role, array $data): bool;

    /**
     * Delete a role
     *
     * @param Role $role
     * @return bool
     */
    public function deleteRole(Role $role): bool;

    /**
     * Get all roles for assignment
     *
     * @return Collection
     */
    public function getAllRoles(): Collection;

    /**
     * Get roles by user
     *
     * @param int $userId
     * @return Collection
     */
    public function getRolesByUser(int $userId): Collection;

    /**
     * Search roles by name
     *
     * @param string $searchTerm
     * @param int $limit
     * @return Collection
     */
    public function searchRoles(string $searchTerm, int $limit = 20): Collection;

    /**
     * Get role statistics
     *
     * @return array
     */
    public function getRoleStatistics(): array;

    /**
     * Assign permissions to role
     *
     * @param int $roleId
     * @param array $permissionIds
     * @return bool
     */
    public function assignPermissionsToRole(int $roleId, array $permissionIds): bool;

    /**
     * Remove permissions from role
     *
     * @param int $roleId
     * @param array $permissionIds
     * @return bool
     */
    public function removePermissionsFromRole(int $roleId, array $permissionIds): bool;

    /**
     * Assign role to user
     *
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
    public function assignRoleToUser(int $userId, int $roleId): bool;

    /**
     * Remove role from user
     *
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
    public function removeRoleFromUser(int $userId, int $roleId): bool;

    /**
     * Get role with permissions
     *
     * @param int $roleId
     * @return Role|null
     */
    public function getRoleWithPermissions(int $roleId): ?Role;

    /**
     * Get users count by role
     *
     * @param int $roleId
     * @return int
     */
    public function getUsersCountByRole(int $roleId): int;
}