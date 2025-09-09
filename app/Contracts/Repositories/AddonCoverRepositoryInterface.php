<?php

namespace App\Contracts\Repositories;

use App\Models\AddonCover;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AddonCoverRepositoryInterface
{
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator;
    
    public function create(array $data): AddonCover;
    
    public function update(AddonCover $addonCover, array $data): AddonCover;
    
    public function delete(AddonCover $addonCover): bool;
    
    public function findById(int $id): ?AddonCover;
    
    public function updateStatus(int $id, int $status): bool;
    
    public function getActive(): Collection;
    
    public function getAllForExport(): Collection;
}