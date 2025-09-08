<?php

namespace App\Contracts\Repositories;

use App\Models\InsuranceCompany;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InsuranceCompanyRepositoryInterface
{
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator;
    
    public function create(array $data): InsuranceCompany;
    
    public function update(InsuranceCompany $insuranceCompany, array $data): InsuranceCompany;
    
    public function delete(InsuranceCompany $insuranceCompany): bool;
    
    public function findById(int $id): ?InsuranceCompany;
    
    public function updateStatus(int $id, int $status): bool;
    
    public function getActive(): Collection;
    
    public function getAllForExport(): Collection;
}