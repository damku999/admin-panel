<?php

namespace App\Services;

use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Exports\UserExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

/**
 * User Service
 *
 * Handles User business logic including role management and password handling.
 * Inherits transaction management from BaseService.
 */
class UserService extends BaseService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function getUsers(Request $request): LengthAwarePaginator
    {
        return $this->userRepository->getPaginated($request);
    }

    public function createUser(array $data): User
    {
        return $this->createInTransaction(function () use ($data) {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user = $this->userRepository->create($data);

            if (isset($data['roles'])) {
                $user->assignRole($data['roles']);
            }

            return $user;
        });
    }

    public function updateUser(User $user, array $data): User
    {
        return $this->updateInTransaction(function () use ($user, $data) {
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $updatedUser = $this->userRepository->update($user, $data);

            if (isset($data['roles'])) {
                $updatedUser->syncRoles($data['roles']);
            }

            return $updatedUser;
        });
    }

    public function deleteUser(User $user): bool
    {
        return $this->deleteInTransaction(
            fn() => $this->userRepository->delete($user)
        );
    }

    public function updateStatus(int $userId, int $status): bool
    {
        return $this->updateInTransaction(
            fn() => $this->userRepository->updateStatus($userId, $status)
        );
    }

    public function assignRoles(User $user, array $roles): void
    {
        $this->executeInTransaction(function () use ($user, $roles) {
            $user->syncRoles($roles);
        });
    }

    public function exportUsers(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new UserExport, 'users.xlsx');
    }

    public function getActiveUsers(): Collection
    {
        return $this->userRepository->getActive();
    }

    public function changePassword(User $user, string $newPassword): bool
    {
        return $this->updateInTransaction(function () use ($user, $newPassword) {
            $hashedPassword = Hash::make($newPassword);
            return $this->userRepository->updatePassword($user, $hashedPassword);
        });
    }
    
    public function getUserWithRoles(int $userId): ?User
    {
        return $this->userRepository->findWithRoles($userId);
    }
    
    public function getStoreValidationRules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile_number' => 'required|numeric|digits:10',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|numeric|in:0,1',
            'new_password' => 'required|min:8|max:16|regex:/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/',
            'new_confirm_password' => 'required|same:new_password',
        ];
    }
    
    public function getUpdateValidationRules(User $user): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile_number' => 'required|numeric|digits:10',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|numeric|in:0,1',
        ];
    }
    
    public function getPasswordValidationRules(): array
    {
        return [
            'new_password' => 'required|min:8|max:16|regex:/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/',
            'new_confirm_password' => 'required|same:new_password',
        ];
    }
    
    public function getRoles(): Collection
    {
        return Role::all();
    }
}