<?php

namespace App\Repositories;

use App\Contracts\Repositories\AddonCoverRepositoryInterface;
use App\Models\AddonCover;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AddonCoverRepository implements AddonCoverRepositoryInterface
{
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = AddonCover::select('*');
        
        if (!empty($request->search)) {
            $search = trim($request->search);
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        
        return $query->paginate($perPage);
    }
    
    public function create(array $data): AddonCover
    {
        return AddonCover::create($data);
    }
    
    public function update(AddonCover $addonCover, array $data): AddonCover
    {
        $addonCover->update($data);
        return $addonCover->fresh();
    }
    
    public function delete(AddonCover $addonCover): bool
    {
        return $addonCover->delete();
    }
    
    public function findById(int $id): ?AddonCover
    {
        return AddonCover::find($id);
    }
    
    public function updateStatus(int $id, int $status): bool
    {
        return AddonCover::where('id', $id)->update(['status' => $status]) > 0;
    }
    
    public function getActive(): Collection
    {
        return AddonCover::where('status', 1)->get();
    }
    
    public function getAllForExport(): Collection
    {
        return AddonCover::all();
    }
}