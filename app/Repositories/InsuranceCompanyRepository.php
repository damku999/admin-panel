<?php

namespace App\Repositories;

use App\Contracts\Repositories\InsuranceCompanyRepositoryInterface;
use App\Models\InsuranceCompany;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InsuranceCompanyRepository implements InsuranceCompanyRepositoryInterface
{
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = InsuranceCompany::select('*');
        
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
    
    public function create(array $data): InsuranceCompany
    {
        return InsuranceCompany::create($data);
    }
    
    public function update(InsuranceCompany $insuranceCompany, array $data): InsuranceCompany
    {
        $insuranceCompany->update($data);
        return $insuranceCompany->fresh();
    }
    
    public function delete(InsuranceCompany $insuranceCompany): bool
    {
        return $insuranceCompany->delete();
    }
    
    public function findById(int $id): ?InsuranceCompany
    {
        return InsuranceCompany::find($id);
    }
    
    public function updateStatus(int $id, int $status): bool
    {
        return InsuranceCompany::where('id', $id)->update(['status' => $status]) > 0;
    }
    
    public function getActive(): Collection
    {
        return InsuranceCompany::where('status', 1)->get();
    }
    
    public function getAllForExport(): Collection
    {
        return InsuranceCompany::all();
    }
}