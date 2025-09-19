<?php

namespace App\Services;

use App\Contracts\Repositories\PermissionRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Services\PermissionServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService extends BaseService implements PermissionServiceInterface
{
    public function __construct(
        private PermissionRepositoryInterface $permissionRepository,
        private RoleRepositoryInterface $roleRepository
    ) {}

    public function getPermissions(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        return $this->permissionRepository->getPermissionsWithFilters($request, $perPage);
    }

    public function createPermission(array $data): Permission
    {
        return $this->createInTransaction(function () use ($data) {
            return $this->permissionRepository->create($data);
        });
    }

    public function updatePermission(Permission $permission, array $data): bool
    {
        return $this->updateInTransaction(function () use ($permission, $data) {
            return $this->permissionRepository->update($permission, $data);
        });
    }

    public function deletePermission(Permission $permission): bool
    {
        return $this->deleteInTransaction(function () use ($permission) {
            // First remove the permission from all roles
            $permission->roles()->detach();

            // Then delete the permission
            return $this->permissionRepository->delete($permission);
        });
    }

    public function getAllPermissions(): Collection
    {
        return $this->permissionRepository->getAllPermissions();
    }

    public function getPermissionsByRole(int $roleId): Collection
    {
        $role = $this->roleRepository->findById($roleId);
        if (!$role) {
            return collect();
        }

        return $role->permissions;
    }

    public function searchPermissions(string $searchTerm, int $limit = 20): Collection
    {
        return $this->permissionRepository->searchPermissions($searchTerm, $limit);
    }

    public function getPermissionStatistics(): array
    {
        return $this->permissionRepository->getPermissionStatistics();
    }

    public function syncRolePermissions(int $roleId, array $permissionIds): bool
    {
        return $this->updateInTransaction(function () use ($roleId, $permissionIds) {
            $role = $this->roleRepository->findById($roleId);
            if (!$role) {
                return false;
            }

            // Sync permissions for the role
            $role->syncPermissions($permissionIds);

            return true;
        });
    }

    public function getPermissionsGroupedByModule(): array
    {
        return $this->permissionRepository->getPermissionsGroupedByModule();
    }
}