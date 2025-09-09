<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = User::select('*');
        
        if (!empty($request->search)) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%');
            });
        }
        
        return $query->paginate($perPage);
    }
    
    public function create(array $data): User
    {
        return User::create($data);
    }
    
    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }
    
    public function delete(User $user): bool
    {
        return $user->delete();
    }
    
    public function findById(int $id): ?User
    {
        return User::find($id);
    }
    
    public function findWithRoles(int $id): ?User
    {
        return User::with('roles')->find($id);
    }
    
    public function updateStatus(int $id, int $status): bool
    {
        return User::where('id', $id)->update(['status' => $status]) > 0;
    }
    
    public function getActive(): Collection
    {
        return User::where('status', 1)->get();
    }
    
    public function getAllForExport(): Collection
    {
        return User::with('roles')->get();
    }
    
    public function updatePassword(User $user, string $hashedPassword): bool
    {
        return $user->update(['password' => $hashedPassword]);
    }
}