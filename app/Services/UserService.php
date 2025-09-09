<?php

namespace App\Services;

use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Exports\UserExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserService implements UserServiceInterface
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
        DB::beginTransaction();
        try {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            
            $user = $this->userRepository->create($data);
            
            if (isset($data['roles'])) {
                $user->assignRole($data['roles']);
            }
            
            DB::commit();
            return $user;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function updateUser(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            
            $updatedUser = $this->userRepository->update($user, $data);
            
            if (isset($data['roles'])) {
                $updatedUser->syncRoles($data['roles']);
            }
            
            DB::commit();
            return $updatedUser;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function deleteUser(User $user): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->userRepository->delete($user);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function updateStatus(int $userId, int $status): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->userRepository->updateStatus($userId, $status);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function assignRoles(User $user, array $roles): void
    {
        DB::beginTransaction();
        try {
            $user->syncRoles($roles);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
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
        DB::beginTransaction();
        try {
            $hashedPassword = Hash::make($newPassword);
            $result = $this->userRepository->updatePassword($user, $hashedPassword);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function getUserWithRoles(int $userId): ?User
    {
        return $this->userRepository->findWithRoles($userId);
    }
}