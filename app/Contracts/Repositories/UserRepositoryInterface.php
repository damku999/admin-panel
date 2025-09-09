<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator;
    
    public function create(array $data): User;
    
    public function update(User $user, array $data): User;
    
    public function delete(User $user): bool;
    
    public function findById(int $id): ?User;
    
    public function findWithRoles(int $id): ?User;
    
    public function updateStatus(int $id, int $status): bool;
    
    public function getActive(): Collection;
    
    public function getAllForExport(): Collection;
    
    public function updatePassword(User $user, string $hashedPassword): bool;
}