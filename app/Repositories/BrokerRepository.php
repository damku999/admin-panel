<?php

namespace App\Repositories;

use App\Contracts\Repositories\BrokerRepositoryInterface;
use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BrokerRepository implements BrokerRepositoryInterface
{
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = Broker::select('*');
        
        if (!empty($request->search)) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhere('mobile_number', 'LIKE', '%' . $search . '%');
            });
        }
        
        return $query->paginate($perPage);
    }
    
    public function create(array $data): Broker
    {
        return Broker::create($data);
    }
    
    public function update(Broker $broker, array $data): Broker
    {
        $broker->update($data);
        return $broker->fresh();
    }
    
    public function delete(Broker $broker): bool
    {
        return $broker->delete();
    }
    
    public function findById(int $id): ?Broker
    {
        return Broker::find($id);
    }
    
    public function updateStatus(int $id, int $status): bool
    {
        return Broker::where('id', $id)->update(['status' => $status]) > 0;
    }
    
    public function getActive(): Collection
    {
        return Broker::where('status', 1)->get();
    }
    
    public function getAllForExport(): Collection
    {
        return Broker::all();
    }
}