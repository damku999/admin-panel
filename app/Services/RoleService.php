<?php

namespace App\Services;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\RoleServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class RoleService extends BaseService implements RoleServiceInterface
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function getRoles(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        return $this->roleRepository->getRolesWithFilters($request, $perPage);
    }

    public function createRole(array $data): Role
    {
        return $this->createInTransaction(function () use ($data) {
            $role = $this->roleRepository->create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'web'
            ]);

            // Assign permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role;
        });
    }

    public function updateRole(Role $role, array $data): bool
    {
        return $this->updateInTransaction(function () use ($role, $data) {
            $updated = $this->roleRepository->update($role, [
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? $role->guard_name
            ]);

            // Update permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $updated;
        });
    }

    public function deleteRole(Role $role): bool
    {
        return $this->deleteInTransaction(function () use ($role) {
            // First remove the role from all users
            $role->users()->detach();

            // Remove all permissions from the role
            $role->permissions()->detach();

            // Then delete the role
            return $this->roleRepository->delete($role);
        });
    }

    public function getAllRoles(): Collection
    {
        return $this->roleRepository->getAllRoles();
    }

    public function getRolesByUser(int $userId): Collection
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            return collect();
        }

        return $user->roles;
    }

    public function searchRoles(string $searchTerm, int $limit = 20): Collection
    {
        return $this->roleRepository->searchRoles($searchTerm, $limit);
    }

    public function getRoleStatistics(): array
    {
        return $this->roleRepository->getRoleStatistics();
    }

    public function assignPermissionsToRole(int $roleId, array $permissionIds): bool
    {
        return $this->updateInTransaction(function () use ($roleId, $permissionIds) {
            $role = $this->roleRepository->findById($roleId);
            if (!$role) {
                return false;
            }

            $role->givePermissionTo($permissionIds);
            return true;
        });
    }

    public function removePermissionsFromRole(int $roleId, array $permissionIds): bool
    {
        return $this->updateInTransaction(function () use ($roleId, $permissionIds) {
            $role = $this->roleRepository->findById($roleId);
            if (!$role) {
                return false;
            }

            $role->revokePermissionTo($permissionIds);
            return true;
        });
    }

    public function assignRoleToUser(int $userId, int $roleId): bool
    {
        return $this->updateInTransaction(function () use ($userId, $roleId) {
            $user = $this->userRepository->findById($userId);
            $role = $this->roleRepository->findById($roleId);

            if (!$user || !$role) {
                return false;
            }

            $user->assignRole($role);
            return true;
        });
    }

    public function removeRoleFromUser(int $userId, int $roleId): bool
    {
        return $this->updateInTransaction(function () use ($userId, $roleId) {
            $user = $this->userRepository->findById($userId);
            $role = $this->roleRepository->findById($roleId);

            if (!$user || !$role) {
                return false;
            }

            $user->removeRole($role);
            return true;
        });
    }

    public function getRoleWithPermissions(int $roleId): ?Role
    {
        return $this->roleRepository->getRoleWithPermissions($roleId);
    }

    public function getUsersCountByRole(int $roleId): int
    {
        return $this->roleRepository->getUsersCountByRole($roleId);
    }
}